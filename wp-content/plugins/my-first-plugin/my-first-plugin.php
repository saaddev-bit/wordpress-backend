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



/**
 * Renders a secure contact form.
 *
 * @param array $atts Shortcode attributes.
 * @return string The form HTML.
 */

 function mfp_contact_form_shortcode($atts) {
    ob_start();
    ?>
    <form method="post" action="">
        <?php wp_nonce_field( 'mfp_contact_form', 'mfp_nonce' ); ?>
        <label for="mfp_name">Name: </label>
        <input type="text" id="mfp_name" name="mfp_name" required>
        <label for="mfp_message">Message:</label>
        <textarea id="mfp_message" name="mfp_message" required></textarea>
        <input type="submit" name="mfp_submit" value="Submit">
    </form>
    <?php 
    if (isset ($_POST['mfp_submit'])) {
        mfp_process_form();
    }
    return ob_get_clean() ;
 }
add_shortcode( 'mfp_contact_form', 'mfp_contact_form_shortcode' );


/**
 * Processes the contact form submission.
 */


 function mfp_process_form() {
    if ( !isset ( $_POST['mfp_nonce']) || ! wp_verify_nonce( $_POST['mfp_nonce'], 'mfp_contact_form' )){
        echo '<p>Security check failed!</p>';
        return;
    }

    $name = isset( $_POST['mfp_name'] ) ? sanitize_text_field( wp_unslash( $_POST['mfp_name'] ) ) : '';
    $message = isset( $_POST['mfp_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['mfp_message'] ) ) : '';

    if ( empty( $name ) || empty( $message ) ) {
        echo '<p>Please fill out all fields.</p>';
        return;
    }

    $data = "Name: $name, Message: $message";
    if( mfp_insert_data( $data)){
        echo '<p>Form Submitted Successfully!!</p>';
    } else {
        echo '<p>Error submitting form.</p>';
    }
 }