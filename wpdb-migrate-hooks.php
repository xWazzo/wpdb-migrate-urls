<?php

/**
 * Get steps
 *
 * @see wpdb_migrate_template_step_1
 * @see wpdb_migrate_template_step_2
 * @see wpdb_migrate_template_alert
 */
add_action( 'get_step_1', 'wpdb_migrate_template_step_1', 10 );
add_action( 'get_step_2', 'wpdb_migrate_template_step_2', 10 );
add_action( 'wpdb_migrate_alert', 'wpdb_migrate_template_alert', 10 );
