<?php

if( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/interface-cdt-form-field.php';

if (!class_exists('CDT_Email_Form_Field')) {
    class CDT_Email_Form_Field implements CDT_Form_Field {
        private $name;
        private $label;

        public function __construct($name, $label) {
            $this->name = $name;
            $this->label = $label;
        }

        public function render(): string {
            return '<label for="' . $this->name . '">' . $this->label . '<span style="color:red;">*</span></label>' .
                '<input type="email" name="' . $this->name . '" id="' . $this->name . '" /><br />';
        }

        public function validate(): bool {
            return \is_email($_POST[$this->name]);
        }
    }
}