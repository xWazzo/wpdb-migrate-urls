<?php
/**
* Url Validation
* @param    $url            string      (required)          Url to validate
* @return   $url/$message   array       Valid url and validation mesages.
*/
function url_validation($url){
  $validation['url'] = "";

  // Remove all illegal characters from a url
  $url = filter_var($url, FILTER_SANITIZE_URL);

  // Validate url
  if (!filter_var($url, FILTER_VALIDATE_URL) === false){
    /* Check the last string is not '/' */
    $sUrlLastString = substr($url, -1);

    if($sUrlLastString=="/"){
      $validation['text'] .= __("Warning: Remember to not include '/' at the end of $url");
      $validation['type'] = "warning";  

      /* Fix URL */
      $validation['url'] = substr($url, 0, -1);
    }else{
      /* Fix URL */
      $validation['url'] = $url;
    }

  }else{
    $validation['text'] = __("$url is not a valid URL");
    $validation['type'] = "danger";
  }

  return $validation;
}

/**
 * Url Update Walker
 *
 * Walk through the array/object to Update $sOldUrl for $sActualUrl.
 *
 * @param   &$value     array|string (required)       Array or string where function will search the text to update.
 * @param   $key        string                        Key where the value is stored.
 *
*/
function url_update_walker(&$value, $key){
  $sOldUrl    = get_site_url();
  $sActualUrl = $_POST['new-url'];

  /* Validate Url */
  $aOldUrl    = url_validation($sOldUrl);
  $aActualUrl = url_validation($sActualUrl);

  $sOldUrl    = !empty($aOldUrl['url']) ? $aOldUrl['url'] : $sOldUrl;
  $sActualUrl = !empty($aActualUrl['url']) ? $aActualUrl['url'] : $sActualUrl;

  $updated_string = "";

  if( is_array($value) || is_object($value)):
    foreach ($value as $key => $string):

      if($key == "old_value") continue;

        $aUstring = @unserialize($string);
        if ($aUstring !== false){
          array_walk_recursive($aUstring, "url_update_walker");

          $updated_string = serialize($aUstring);
        }

        $bValueIsArray   = is_array($value);
        $bValueIsObject  = is_object($value);

        if($bValueIsArray):
          if (strpos($string, $sOldUrl) !== false) {
            $value['old_value'] = $string;
          }

          $string       = !empty($updated_string) ? $updated_string : $string;
          $value[$key]  = str_replace( $sOldUrl, $sActualUrl, $string );
        elseif($bValueIsObject):
          if (strpos($string, $sOldUrl) !== false) {
            $value->old_value = $string;
          }
        
          $string       = !empty($updated_string) ? $updated_string : $string;
          $value->$key  = str_replace( $sOldUrl, $sActualUrl, $string );
        endif;
     
    endforeach;
  else:
    $value = str_replace( $sOldUrl, $sActualUrl, $value );
  endif;
}

/**
* Update Selected Table
*
* @param   $table       string      (required)        Table name where string will be replaced
* @param   $column      string      (required)        Table column where probably is the string to replace
* @param   $find        string      (required)        String to find
*/
function update_selected_table($table, $column, $find){
  global $wpdb;

  $aUrlOptions = $wpdb->get_results("
      SELECT * 
      FROM $table 
      WHERE $column 
        LIKE '$find'
    ");

  $aUrlOptions = new ObjectArrayAccess($aUrlOptions);

  /* 
   * Run recursively 'url_update_walker' to update $aUrlOptions
   * $sOldUrl to $sActualUrl
   *
   * @see url_update_walker
  */
  array_walk_recursive($aUrlOptions, "url_update_walker");

  /* Update Options */
  foreach ($aUrlOptions as $key => $oOption) {
    global $wpdb;
      
    $sTable_options                   = $wpdb->prefix . "options";
    $sTable_postmeta                  = $wpdb->prefix . "postmeta";

    switch ($table) {
      case $sTable_options:
        $aColumnsNeeded['id']     = "option_id";
        $aColumnsNeeded['name']   = "option_name";
        $aColumnsNeeded['value']  = "option_value";
        break;
      case $sTable_postmeta:
        $aColumnsNeeded['id']     = "post_id";
        $aColumnsNeeded['name']   = "meta_key";
        $aColumnsNeeded['value']  = "meta_value";
        break;
      
      default:
        $aColumnsNeeded['id']     = "option_id";
        $aColumnsNeeded['name']   = "option_name";
        $aColumnsNeeded['value']  = "option_value";
        break;
    }

    $row_id          = $aColumnsNeeded['id'];
    $row_name        = $aColumnsNeeded['name'];
    $row_value       = $aColumnsNeeded['value'];

    $row_id_value    = $oOption->$aColumnsNeeded['id'];
    $row_name_value  = $oOption->$aColumnsNeeded['name'];

    $new_value       = $oOption->$aColumnsNeeded['value'];
    $old_value       = $oOption->old_value;


    $sql[] = "UPDATE $table
              SET $row_value = replace($row_value, '$old_value', '$new_value')
                WHERE $row_id = $row_id_value
                AND $row_name = '$row_name_value';";
  }

  return $sql;
}

/**
* WPDB Migration Status
* 
* Check if the url from the database is the same url from $_POST['new-url']
* 
* @return     $success      (boolean)       True on success
*/
function wpdb_migration_status(){
  if(!isset($_POST['new-url'])) return;

    global $wpdb;

    $sTable_options = $wpdb->prefix . "options";

    $site_url = $wpdb->get_results("
                  SELECT option_value 
                    FROM $sTable_options
                    WHERE option_name = 'siteurl'
                ");

    /* Remove last '/' so it can be equal to $sActualUrl */
    $site_url = $site_url[0]->option_value;
    $sSetURL  = $_POST['new-url'];
    
    $aSiteUrl = url_validation($site_url);
    $site_url = !empty($aSiteUrl['url']) ? $aSiteUrl['url'] : $sSiteUrl;

    $aSetURL  = url_validation($sSetURL);
    $sSetURL  = !empty($aSetURL['url']) ? $aSetURL['url'] : $sSetURL;

    $success  = $site_url == $sSetURL;

    return $success;
}

/**
* Migrate Database URLs
* 
* Run all the functions needed to migrate the database.
* 
* @return $message          Display success or failure message.
*/
function migrate_database_urls(){
  $sOldUrl    = get_site_url();
  $sActualUrl = $_POST['new-url'];

  $aOldUrl    = url_validation($sOldUrl);
  $aActualUrl = url_validation($sActualUrl);

  $sOldUrl    = !empty($aOldUrl['url']) ? $aOldUrl['url'] : $sOldUrl;
  $sActualUrl = !empty($aActualUrl['url']) ? $aActualUrl['url'] : $sActualUrl;

  if(empty($aOldUrl['url']) || empty($aActualUrl['url'])):

    $message['text'] .= $aOldUrl['text'];
    $message['text'] .= $aActualUrl['text'];
    $message['type']  = $aOldUrl['type'];
    $message['type']  = $aActualUrl['type'];

  else:

    if($aOldUrl['url'] == $aActualUrl['url']){
      $message['text'] .= __("Database is up to date. $sOldUrl is equal to $sActualUrl", "wpdb-migrate-urls");
      $message['type']  = "primary";
    }else{
      global $wpdb;
      
      $sTable_options                   = $wpdb->prefix . "options";
      $sTable_posts                     = $wpdb->prefix . "posts";
      $sTable_postmeta                  = $wpdb->prefix . "postmeta";
      $sTable_revslider_slides          = $wpdb->prefix . "revslider_slides";
      $sTable_revslider_static_slides   = $wpdb->prefix . "revslider_static_slides";

      $sql_options  = update_selected_table($sTable_options, "option_value", "%$sOldUrl%");
      $sql_postmeta = update_selected_table($sTable_postmeta, "meta_value", "%$sOldUrl%");

      if(is_array($sql_options) && is_array($sql_postmeta)){
        $sql = array_merge($sql_options, $sql_postmeta);
      }elseif(is_array($sql_options)){
        $sql = $sql_options;
      }else{
        $sql = $sql_postmeta;
      }

      /* Update RevSlider */
      $sql[] = "UPDATE $sTable_posts
                  SET guid = replace(guid, '$sOldUrl','$sActualUrl');";

      $sql[] = "UPDATE $sTable_posts
                  SET post_content = replace(post_content, '$sOldUrl', '$sActualUrl');";

      $sql[] = "UPDATE $sTable_postmeta
                  SET meta_value = replace(meta_value,'$sOldUrl','$sActualUrl');";

      /* Update RevSlider */
      $sql[] = "UPDATE $sTable_revslider_slides
                  SET params = REPLACE(params, '$sOldUrl', '$sActualUrl')
                  WHERE params LIKE '%$sOldUrl%';";

      $sql[] = "UPDATE $sTable_revslider_slides
                  SET layers = REPLACE(layers, '$sOldUrl', '$sActualUrl')
                  WHERE layers LIKE '%$sOldUrl%';";

      $sql[] = "UPDATE $sTable_revslider_static_slides
                  SET layers = REPLACE(layers, '$sOldUrl', '$sActualUrl')
                  WHERE layers LIKE '%$sOldUrl%';";

      /**
      * This section make the query.
      */
      $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
      mysqli_set_charset($mysqli, 'utf8');

      foreach ($sql as $key => $sSql) {
        $mysqli->query($sSql);
      }

      $success = wpdb_migration_status();

      if($success){
        $message['text'] .= __("Database was successfully updated from: <b>$sOldUrl</b> to <b>$sActualUrl</b>", "wpdb-migrate-urls");
        $message['type'] = "success";
      }else{
        $message['text'] .= __("It looks like something is wrong, Old Url is diferent from New Url: <b>$sOldUrl</b> to <b>$sActualUrl</b>", "wpdb-migrate-urls");
        $message['type'] = "warning";
      }
      
      return $message;
    }
  endif;

  return $message;
}