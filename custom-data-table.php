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

add_action('init', function() {
    new Custom_User_List_Table();
});

function enqueue_table_styles() {
    wp_enqueue_style('jquery-table-style', CUSTOM_DATA_TABLE_DIR . 'assets/table-style.css', array('jquery-datatable-css'));
}
add_action('wp_enqueue_scripts', callback: 'enqueue_table_styles' );

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