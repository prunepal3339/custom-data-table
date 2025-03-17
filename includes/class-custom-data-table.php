<?php
if ( !defined('ABSPATH') ) {
    exit;
}

if( !class_exists('Custom_Data_Table') ) {
    class Custom_Data_Table {
        private $table_id;
        private $columns;
        private $data;
        private $options;

        public function __construct($table_id, $columns, $data, $options) {
            $this->table_id = $table_id;
            $this->columns = $columns;
            $this->data = $data;
            $this->options = $options;

            add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

            add_shortcode( $this->table_id, array($this, 'render_shortcode') );
        }
        public function enqueue_scripts() {
            wp_enqueue_script('jquery');

            //Add jQuery datatables
            wp_enqueue_script('jquery-datatable-css', 'https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css');
            wp_enqueue_script('jquery-datatable-js', 'https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js', array('jquery') );

            //Initialize DataTables
            $options = json_encode(apply_filters('custom_data_table_options', $this->options));

            wp_add_inline_script('jquery-datatable-js', "
                jQuery(document).ready(function($) {
                    $('#{$this->table_id}').DataTable($options);
                });
            ");
        }
        public function render() {
            ?>
            <table id="<?php echo esc_attr($this->table_id); ?>" class="display cell-border" style="width:100%">
                <thead>
                    <tr><?php $this->render_table_columns(); ?></tr>
                </thead>
                <tbody>
                    <?php $this->render_table_rows(); ?></tr>
                </tbody>
            </table>
            <?php
        }
        private function render_table_columns() {
            foreach ($this->columns as $column) {
                $column = apply_filters('custom_data_table_column', $column, $this->table_id);
                echo '<th>' . esc_html($column) . '</th>';
            }
        }
        private function render_table_rows() {
            foreach ($this->data as $row) {
                $row = apply_filters('custom_data_table_row', $row, $this->table_id);

                echo '<tr>';

                foreach ($row as $cell) {
                    $cell = apply_filters('custom_data_table_cell', $cell, $this->table_id);
                    echo '<td>' . esc_html($cell) . '</td>';
                }
                echo '</tr>';
            }
        }
        public function render_shortcode() {
            ob_start();
            ?>
            <div class='frontend-data-table' id='<?php echo "frontend-data-table__{$this->table_id}" ?>' >
                <?php $this->render(); ?>
            </div>
            <?php
            return ob_get_clean();
        }
    }
}