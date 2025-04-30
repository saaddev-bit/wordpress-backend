<?php
/*
Plugin Name: My First Plugin
Description: A simple WordPress plugin for learning backend development.
Version: 1.0
Author: Saad Waheed
*/

/** 
 *Activates the plugin and sets up the database table.
*/
function mfp_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mfp_data';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    data varchar(255) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY  (id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    update_option( 'mfp_version', '1.0');
    error_log( 'My First Plugin activated' );
}
register_activation_hook( __FILE__, 'mfp_activate' );

/**
 * Inserts data into the custom table.
 *
 * @param string $data The data to insert.
 * @return int|bool The insert ID or false on failure.
 */
function mfp_insert_data($data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'mfp_data';
    return $wpdb->insert(
        $table_name,
        ['data' => $data],
        ['%s']
    );
}
add_action( 'init' , function() {
    mfp_insert_data('Test data');
});

/**
 * Shortcode to display a hello message.
 *
 * @return string The hello message.
 */
function mfp_shortcode() {
    return 'Hello, this is my first plugin!';
}
add_shortcode( 'mfp_hello', 'mfp_shortcode' );


/**
 * Deactivation Hook
 */
function mfp_deactivate() {
    error_log( 'My First Plugin deactivated' );
}
register_deactivation_hook( __FILE__, 'mfp_deactivate' );