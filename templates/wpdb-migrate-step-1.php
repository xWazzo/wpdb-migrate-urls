<?php

$sOldUrl        = get_site_url();
$sActualUrl     = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}"; 
$sFullActualUrl = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}"; 

$aOldUrl    = url_validation($sOldUrl);
$aActualUrl = url_validation($sActualUrl);

$sOldUrl    = $aOldUrl['url'];
$sActualUrl = $aActualUrl['url'];

if($sOldUrl == $sActualUrl){
  $sMessage     = __('It looks like your database has your current url.', "wpdb-migrate-urls");
  $sMessageType = "success";
}

/* Validation Message */
if(!empty($sMessage)){
  $aAlert = array(
    'type'      => $sMessageType,
    'message'   => $sMessage
    );
  
  /**
  * wpdb_migrate_alert hook.
  *
  * @hooked wpdb_migrate_template_alert - 10
  */
  do_action('wpdb_migrate_alert', $aAlert);
} ?>

<form action="" method="post" style="padding: 10px; background-color: #efefef;">
  <div style="width:90%; margin: auto; display: table;"><?php 
    if(!isset($_POST['new-url'])): ?>
      <p style="margin:0; padding: 5px 10px;">
        URL in the database is: <b><?php echo $sOldUrl; ?></b> and your current url is: <b><?php echo $sActualUrl; ?></b>
      </p><?php
    endif; ?>
  </div>
  <div style="width:90%; margin: auto; display: table;">
    <div style="width: 50%; float: left; padding: 0 15px;"><?php 
      if(!isset($_POST['new-url'])): ?>
        <input type="text" name="new-url" id="new-url" value="" placeholder="<?php echo $sActualUrl; ?>" /><?php
      else: 
        $success    = wpdb_migration_status();

        $GithubUrl   = "https://github.com/xWazzo";
        $FenUrl      = "https://frontend.ninja";
        $step2Text   = __('Well done! :D Piece of cake, right?', "wpdb-migrate-urls");
        $step2Text  .= __("<br> <a href='$GithubUrl' target='_blank'>Fork us on Github</a>", "wpdb-migrate-urls");
        $step2Text  .= __("<br> Wordpress database Migrate was made by xWaZzo @<a href='$FenUrl' target='_blank'>frontend.ninja</a>", "wpdb-migrate-urls");
        echo "<p>$step2Text</p>";
      endif; ?>
    </div>
    <div style="width: 50%; float: left; padding: 0 15px;"><?php
      if(!isset($_POST['new-url'])): ?>
        <input id="submit-url" type="submit" name="submit-url" value="<?php echo __( 'Migrate Database URLs!', "wpdb-migrate-urls" ); ?>" />
        <button name="wpdb-migrate-hide" id="wpdb-migrate-hide" value="true"><?php echo __('Hide this forever', "wpdb-migrate-urls"); ?></button><?php 
      else:
        $sMigrateAgainUrl = $sFullActualUrl;
        echo "<a href='$sMigrateAgainUrl'>Run again</a>";
      endif;
      wp_nonce_field( 'wpdb_migrate_database', '_wpdb_mdb_nonce' ); ?>
    </div>
  </div>
</form>