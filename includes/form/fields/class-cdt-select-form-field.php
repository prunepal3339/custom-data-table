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
                   '<select name=' . $this->name . 'id = ' . $this->name .  '>  <option disabled selected value> -- select an option -- </option>
 ' . $this->options_html() . '</select><br />';
        }

        public function validate(): bool {
            return !empty($_POST[$this->name]) && in_array($_POST[$this->name], array_column($this->options, 'value'));
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