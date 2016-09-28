<?php

if(!wp_verify_nonce( $_POST['_wpdb_mdb_nonce'], 'wpdb_migrate_database' )){
  $sMessage     .= __('Please use the form submit button', "wpdb-migrate-urls");
  $sMessageType  = "warning";
}else{
  $aMessage = migrate_database_urls();

  $sMessage     .= $aMessage['text'];
  $sMessageType  = $aMessage['type'];
}

if(isset($_POST['wpdb-migrate-hide'])){
  add_option( "wpdb_migrate_hide", true );

  $sMessage      = __('You won\'t see this form again.', "wpdb-migrate-urls");
  $sMessageType  = "warning";
}

/* Validation Message */
if(!empty($sMessage)){
  $aAlert = array(
    'message'   => $sMessage,
    'type'      => $sMessageType
    );
  
  /**
  * wpdb_migrate_alert hook.
  *
  * @hooked wpdb_migrate_template_alert - 10
  */
  do_action('wpdb_migrate_alert', $aAlert);
}