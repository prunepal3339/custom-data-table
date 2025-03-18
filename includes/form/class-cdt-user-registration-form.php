<?php

if( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/class-cdt-abstract-form.php';
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

            add_action('wp_ajax_test_another_action', function() {
                wp_send_json_success(['message' => 'Hello!']);
            });
            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_form_handler_scripts') );
        }

        public function enqueue_form_handler_scripts() {

            wp_enqueue_script('form_handler', CUSTOM_DATA_TABLE_URL . 'assets/js/form_handler.js');

            wp_localize_script('form_handler', 'ajaxObject', array(
                'formId' => self::$form_id,
                'actionParam' => self::$ajax_action,
                'adminUrl' => admin_url('admin-ajax.php'),
                'nonceField' => wp_create_nonce('user_registration_nonce'),
            ));
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

        public function handle_form() {

            $formData = $_POST['formData'];            
            if ( ! isset($formData['user_registration_nonce_field']) || ! wp_verify_nonce($formData['user_registration_nonce_field'], 'user_registration_nonce') ) {
                wp_send_json_error(['message' => __('Nonce invalid, Do not try to do evil things with us.', 'custom-data-table')]);
                return;
            }
            
            $validated = true;
            foreach($this->form_fields as $field) {
                if ( !$validated ) break;
                $validated = $validated && $field->validate();
            }
            $validated = $validated && $formData['confirm-password'] == $formData['password'];
            if ( !$validated ) {
                wp_send_json_error(['message' => 'Form validation failed!']);
            }
            
            $userdata = [];

            $userdata['display_name'] = sanitize_text_field($formData['displayname']);
            $userdata['user_login'] = sanitize_text_field($formData['username']);
            $userdata['user_email'] = sanitize_email($formData['email']);
            $userdata['password'] = sanitize_text_field($formData['password']);
            $userdata['role'] = sanitize_text_field($formData['roleselectid']);

            wp_insert_user($userdata);

            wp_send_json_success(['message' => __('User registered successfully!', 'custom-data-table')]);
            wp_die();
        }
    }
}

CDT_User_Registration_Form::init();