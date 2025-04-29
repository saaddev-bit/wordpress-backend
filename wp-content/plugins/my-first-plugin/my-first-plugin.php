<?php
/*
Plugin Name: My First Plugin
Description: A simple WordPress plugin for learning backend development.
Version: 1.0
Author: Saad Waheed
*/

//Activation Hook
function mfp_activate() {
    update_option( 'mfp_version', '1.0');
    error_log( 'My First Plugin activated' );
}
register_activation_hook( __FILE__, 'mfp_activate' );


function mfp_shortcode() {
    return 'Hello, this is my first plugin!';
}
add_shortcode( 'mfp_hello', 'mfp_shortcode' );




//Deactivation Hook
function mfp_deactivate() {
    error_log( 'My First Plugin deactivated' );
}
register_deactivation_hook( __FILE__, 'mfp_deactivate' );