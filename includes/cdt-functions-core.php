<?php
if (!function_exists('cdt_is_valid_password')) {
    /**
     * Regular Expression Based Password Validation
     *
     * The regex validates a password based on the following criteria:
     * 1. The password must be at least 8 characters long.
     * 2. It must contain at least one uppercase letter (A-Z).
     * 3. It must contain at least one lowercase letter (a-z).
     * 4. It must contain at least one special character from the set (!@#$%^&*(),.?":{}|<>).
     *
     * @param string $password The password string to validate.
     * @return bool True if the password meets the criteria, false otherwise.
     */
    function cdt_is_valid_password($password) {
        $pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\W_]).{8,}$/';
        return preg_match($pattern, $password);
    }
}
if ( !function_exists('debug_all_shortcodes') ) {
    function debug_all_shortcodes() {
        global $shortcode_tags;
        if (empty($shortcode_tags)) {
            echo 'No shortcodes are registered.';
        }
        ob_start();
        echo "<table>";
        echo "<thead><tr><th>Shortcode</th><th>Rendered Output</th></tr></thead><tbody>";
        foreach ($shortcode_tags as $shortcode => $callback) {
            $rendered_output = do_shortcode("[$shortcode]");
            echo "<tr>";
            echo "<td style='border: 1px solid #000; padding: 8px;'><strong>[$shortcode]</strong></td>";
            echo "<td style='border: 1px solid #000; padding: 8px;'>" . esc_html($rendered_output) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";

        $output = ob_get_clean();

        wp_die($output);
    }
}

if ( !function_exists('debug_ajax_actions') ) {
    function debug_ajax_actions() {
        global $wp_filter;
    
        ob_start();
    
        echo '<h2>Available AJAX Actions and Callbacks</h2>';
        echo '<table border="1" cellpadding="5">';
        echo '<thead><tr><th>Action Name</th><th>Callback Function</th></tr></thead>';
        echo '<tbody>';
    
        foreach ($wp_filter as $hook => $callback) {
            if (strpos($hook, 'wp_ajax_') === 0 || strpos($hook, 'wp_ajax_nopriv_') === 0) {
                foreach ($callback as $priority => $callbacks) {
                    foreach ($callbacks as $callback) {
                        if (is_array($callback['function'])) {
                            $callback_name = is_object($callback['function'][0]) ? 
                                get_class($callback['function'][0]) . '::' . $callback['function'][1] : 
                                $callback['function'][0] . '::' . $callback['function'][1];
                        } else {
                            $callback_name = $callback['function'];
                        }
                        echo '<tr><td>' . esc_html($hook) . '</td><td>' . '</td></tr>';
                    }
                }
            }
        }
    
        echo '</tbody></table>';
    
        wp_die(ob_get_clean());
    }    
}