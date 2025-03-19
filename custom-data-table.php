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

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-email-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-password-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-text-input-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-select-form-field.php';


require_once CUSTOM_DATA_TABLE_DIR . 'includes/cdt-functions-core.php';

function cdt_user_registration_form_shortcode_cb(): string {
    $urForm = CDT_User_Registration_Form::init();
    
    $role_options = array();

    $roles = new WP_Roles();
    $roles_names_array = $roles->get_names();
    foreach( $roles_names_array as $role ) {
        $role_options[] = array(
            'label' => $role,
            'value' => $role,
        );
    }

    $urForm
        ->add_form_field(new CDT_Text_Input_Form_Field('username', 'Username'))
        ->add_form_field(new CDT_Text_Input_Form_Field('displayname', 'Display Name'))
        ->add_form_field(new CDT_Select_Form_Field('roleselect', 'Select Role', $role_options))
        ->add_form_field(new CDT_Email_Form_Field('email', 'Email'))
        ->add_form_field(new CDT_Password_Form_Field('password', 'Password'))
        ->add_form_field(new CDT_Password_Form_Field('confirm-password', 'Confirm Password'));
    return $urForm->render_form();
}
add_action('init', function() {
    new Custom_User_List_Table();
    add_shortcode('cdt_form_user_registration', 'cdt_user_registration_form_shortcode_cb');
    // error_log(print_r(wp_doing_ajax()));
});

add_filter('custom_data_table_classes', function($classes) {
    $classes[] = 'cell-border';
    return $classes;
});

add_filter('custom_data_table_options', function($options) {
    $options['retrieve'] = true; 
});
