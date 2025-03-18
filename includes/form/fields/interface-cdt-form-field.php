<?php

if( !defined('ABSPATH') ) {
    exit;
}

if( !interface_exists('CDT_Form_Field') ) {
    interface CDT_Form_Field {
        function render(): string;
        function validate(): bool;
    }
}
