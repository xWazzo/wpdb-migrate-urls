<?php

if( ! function_exists('wpdb_migrate_template_step_1') ){
  function wpdb_migrate_template_step_1(){
    include(dirname( __FILE__ ) . '/templates/wpdb-migrate-step-1.php');
  }
}

if( ! function_exists('wpdb_migrate_template_step_2') ){
  function wpdb_migrate_template_step_2(){
    include(dirname( __FILE__ ) . '/templates/wpdb-migrate-step-2.php');
  }
}

if( ! function_exists('wpdb_migrate_template_alert') ){
  function wpdb_migrate_template_alert($aAlert){
    
    extract( $aAlert );

    include(dirname( __FILE__ ) . '/templates/wpdb-migrate-alert.php');
  }
}