<?php

if( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/class-cdt-abstract-form.php';

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-email-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-password-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-text-input-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/class-cdt-select-form-field.php';


if (!class_exists('CDT_User_Registration_Form')) {
    class CDT_User_Registration_Form extends CDT_Abstract_Form {
        private static $form_id = 'user_registration_form';
        private static $ajax_action = 'cdt_user_registration_form_submit';
        private static $_instance = null;
        public static function init() {
            if( !self::$_instance ) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        private function __construct() {
            add_action( "wp_ajax_" . self::$ajax_action ."", array($this, 'handle_form') );
            add_action( "wp_ajax_nopriv_" .self::$ajax_action."", array($this, 'handle_form') );

            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_form_handler_scripts') );

            $this->initialize_form_fields();
        }

        public function enqueue_form_handler_scripts() {

            wp_enqueue_script( 'form_handler', CUSTOM_DATA_TABLE_URL . 'assets/js/form_handler.js', array('jquery'), filemtime(__FILE__) );

            wp_localize_script( 'form_handler', 'ajaxObject', array(
                'formId' => self::$form_id,
                'actionParam' => self::$ajax_action,
                'adminUrl' => admin_url('admin-ajax.php'),
                'nonceField' => wp_create_nonce('user_registration_nonce'),
            ));

            wp_enqueue_style( 'cdt-form-style', CUSTOM_DATA_TABLE_URL . 'assets/css/form.css' );
        }
        
        public function render_form() {
            $rendered_form = '<form id=' . self::$form_id . ' method="post">';
            $rendered_form .= wp_nonce_field('user_registration_nonce', 'user_registration_nonce_field');
            foreach ($this->form_fields as $field) {
                $rendered_form .= $field->render();
            }
            $rendered_form .= '<input type="submit" name="submit" value="Register" />';
            $rendered_form .= '</form>';
            return $rendered_form;
        }

        public function initialize_form_fields() {
            $role_options = array();

            $roles = new WP_Roles();
            $roles_names_array = $roles->get_names();
            foreach( $roles_names_array as $role ) {
                $role_options[] = array(
                    'label' => $role,
                    'value' => $role,
                );
            }
        
            $this
                ->add_form_field(new CDT_Text_Input_Form_Field('username', 'Username'))
                ->add_form_field(new CDT_Text_Input_Form_Field('displayname', 'Display Name'))
                ->add_form_field(new CDT_Select_Form_Field('roleselect', 'Select Role', $role_options))
                ->add_form_field(new CDT_Email_Form_Field('email', 'Email'))
                ->add_form_field(new CDT_Password_Form_Field('password', 'Password'))
                ->add_form_field(new CDT_Password_Form_Field('confirm-password', 'Confirm Password'))
            ;
                
        }

        public function handle_form() {
            $formData = $_POST['formData'];
            if ( ! isset($formData['user_registration_nonce_field']) || !wp_verify_nonce($formData['user_registration_nonce_field'], 'user_registration_nonce') ) {
                wp_send_json_error(['message' => __('Nonce invalid, Do not try to do evil things with us.', 'custom-data-table')]);
                return;
            }
            //reset post to access formData only
            $_POST = $formData;

            global $cdt_form_errors;
            $cdt_form_errors = [];

            $validated = true;

            foreach($this->form_fields as $field) {
                $validation = $field->validate();
                $validated = $validated  && $validation;
            }

            if ( !$validated ) {
                wp_send_json_error(value: implode('<br>', $cdt_form_errors));
                return;
            }
            
            $userdata = [];

            $userdata['display_name'] = sanitize_text_field($formData['displayname']);
            $userdata['user_login'] = sanitize_text_field($formData['username']);
            $userdata['user_email'] = sanitize_email($formData['email']);
            $userdata['password'] = sanitize_text_field($formData['password']);
            $userdata['role'] = sanitize_text_field(lcfirst($formData['roleselect']));
            
            if ($formData['confirm-password'] != $formData['password']) {
                wp_send_json_error(__('Passwords do not match', 'custom-data-table'));
                return;
            }

            if ( get_user_by('login', $userdata['user_login']) ) {
                wp_send_json_error(__('Username is already in use!', 'custom-data-table'));
                return;
            }

            if ( get_user_by('email', $userdata['user_email']) ) {
                wp_send_json_error(__('Email is already in use', 'custom-data-table'));
                return;
            }

            wp_insert_user( $userdata );

            wp_send_json_success(['message' => __('User registered successfully!', 'custom-data-table')]);
            wp_die();
        }
    }

}

CDT_User_Registration_Form::init();