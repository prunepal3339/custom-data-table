<?php

if( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/interface-cdt-form-field.php';
require_once CUSTOM_DATA_TABLE_DIR . 'includes/cdt-functions-core.php';
if(!class_exists('CDT_Password_Form_Field')) {
    class CDT_Password_Form_Field implements CDT_Form_Field {
        private $name;
        private $label;

        public function __construct($name, $label) {
            $this->name = $name;
            $this->label = $label;
        }

        public function render(): string {
            return '<label for="' . $this->name . '">' . $this->label . '<span style="color:red;">*</span></label>' .
                '<input type="password" name="' . $this->name . '" id="' . $this->name . '" /><br />';
        }

        public function validate(): bool {
            return \cdt_is_valid_password($_POST[$this->name]);
        }
    }
}