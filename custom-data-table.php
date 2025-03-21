<?php
/**
 * Plugin Name: Custom Data Table for Frontend
 * Description: Data Table for rendering with jQuery DataTables on the frontend, supports hooks for advanced decorations.
 * Version: 1.0
 * Author: Purshottam Nepal
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define('CUSTOM_DATA_TABLE_DIR', plugin_dir_path(__FILE__));
define('CUSTOM_DATA_TABLE_URL', plugin_dir_url(__FILE__));

require_once CUSTOM_DATA_TABLE_DIR . 'includes/class-custom-user-list-table.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/class-cdt-user-registration-form.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/cdt-functions-core.php';


function cdt_user_registration_form_shortcode_cb( $atts ): string {
    
    shortcode_atts( array(
        'admin_only_message' => 'Only admins are allowed to see this content',
    ), $atts, 'cdt_form_user_registration' );
    
    if ( is_user_logged_in() && !in_array('administrator', wp_get_current_user()->roles) ) {
        return '<p>' . esc_html($atts['admin_only_message']) . '</p>';
    }
    
    $urForm = CDT_User_Registration_Form::init();
    return $urForm->render_form();
}
add_action('init', function() {
    new Custom_User_List_Table();
    add_shortcode('cdt_form_user_registration', 'cdt_user_registration_form_shortcode_cb');

});

add_filter('custom_data_table_classes', function( $classes ) {
    $classes[] = 'cell-border';
    return $classes;
});
