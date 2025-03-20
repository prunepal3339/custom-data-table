<?php

if( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once CUSTOM_DATA_TABLE_DIR . 'includes/form/fields/interface-cdt-form-field.php';

if(!class_exists('CDT_Select_Form_Field')) {
    class CDT_Select_Form_Field implements CDT_Form_Field {
        private $name;
        private $label;
        private $options;
        public function __construct($name, $label, $options) {
            $this->name = $name;
            $this->label = $label;
            $this->options = $options;
        }
    
        public function render(): string {
            return '<label for="' . $this->name . '">' . $this->label . '<span style="color:red;">*</span></label>' .
                   '<select name=' . $this->name . ' id = ' . $this->name .  ' required>  <option disabled selected value> -- select an option -- </option>
 ' . $this->options_html() . '</select><br />';
        }
        
        public function validate(): bool {
            global $cdt_form_errors;

            if ( empty($_POST[$this->name]) ) {
                $cdt_form_errors[] = __("{$this->name} field is empty", 'custom-data-table');
                return false;
            }
            if ( !in_array($_POST[$this->name], array_column($this->options, "value"))) {
                $cdt_form_errors[] = __("Invalid option for {$this->name} field", 'custom-data-table');
                return false;
            }
            return true;
        }
        private function options_html(): string {
            $result = '';
            foreach($this->options as $option) {
                $result .= '<option value = ' . $option['value'] . '>' . $option['label'] . '</option>'; 
            }
            return $result;
        }
    }
}