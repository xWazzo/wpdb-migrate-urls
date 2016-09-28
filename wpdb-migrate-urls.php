<?php 

if(!isset($_GET['wpdb_migrate'])) return;

  $wpdb_migrate_hide = get_option("wpdb_migrate_hide");

  if($wpdb_migrate_hide) return;
      
    require_once( 'class/class_objectArrayAccess.php' );
    require_once( 'migrate-functions.php' );
    require_once( 'wpdb-migrate-template-functions.php' );
    require_once( 'wpdb-migrate-hooks.php' );

    /**
    * get_step_1 hook.
    *
    * @hooked fen_template_step_1 - 10
    */
    do_action('get_step_1');

    if (isset($_POST['new-url'])) {
      /**
      * get_step_2 hook.
      *
      * @hooked fen_template_step_2 - 10
      */
      do_action('get_step_2');

    }






