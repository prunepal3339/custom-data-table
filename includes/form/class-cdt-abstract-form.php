<?php
if (!class_exists('CDT_Abstract_Form')) {
    abstract class CDT_Abstract_Form {
        protected array $form_fields = [];
        abstract public function render_form();
        public function add_form_field(CDT_Form_Field $field): self {
            $this->form_fields[] = $field;
            return $this;
        }
    }
}
