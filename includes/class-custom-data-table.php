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

        public function __construct($table_id, $columns, $data, $options = []) {

            $this->table_id = $table_id;
            $this->columns = $columns;
            $this->data = $data;
            $this->options = $options;

            add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );

            //pagination handler ajax 
            add_action('wp_ajax_cdt_page_data', array($this, 'handle_pagination_ajax_cb'));

            add_shortcode( $this->table_id, array($this, 'render_shortcode') );
        }

        public function enqueue_scripts() {

            wp_enqueue_style('dashicons'); //
            
            wp_enqueue_style('table-css', CUSTOM_DATA_TABLE_URL . 'assets/css/tables.css', array(), filemtime( CUSTOM_DATA_TABLE_DIR . 'assets/css/tables.css') );
            wp_enqueue_script('table-js', CUSTOM_DATA_TABLE_URL . 'assets/js/tables.js', array('jquery'), filemtime( CUSTOM_DATA_TABLE_DIR . 'assets/js/tables.js') );

            $securityNonce = wp_create_nonce('cdt_page_data_action');
            wp_localize_script('table-js', 'my', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'securityNonce' => $securityNonce,
                'tableId' => $this->table_id
            ]);

        }

        public function render() {
            ?>
            <table id="<?php echo esc_attr($this->table_id); ?>" class="<?php echo implode(' ', apply_filters('custom_data_table_classes', [])); ?>" style="width:100%;">
                <thead>
                    <tr><?php $this->render_table_columns(); ?></tr>
                </thead>
                <tbody>
                    <?php $this->render_table_rows(); ?></tr>
                </tbody>
            </table>
            <div class="pagination">
                <?php
                    $numRecords = count($this->data);
                    $numPages = $numRecords / 10;
                
                    if ( $numPages > 1 ) {
                ?>
                <a href="#" class="prev">&laquo;</a>
                <?php for($i = 0; $i < $numPages; $i++) {
                ?>
                <a href="#" class="page"><?php echo $i + 1; ?> </a>
                <?php } ?>
                <a href="#" class="next">&raquo;</a>
                <?php } ?>
            </div>
            <?php
        }

        private function render_table_columns() {
            foreach ($this->columns as $column) {
                $column = apply_filters('custom_data_table_column', $column, $this->table_id);

                $ordering = isset($this->options[$column]['ordering']) && $this->options[$column]['ordering'];
                $filtering = isset($this->options[$column]['filtering']) && $this->options[$column]['filtering'];
                echo '<th>';
                echo "<span class='column'>" . esc_html($column) . "</span>";
            
                if ($ordering) {
                    echo ' <span class="order-icons">
                                <a class="asc">&#9650;</a> 
                                <a class="desc">&#9660;</a>
                            </span>';
                }

                if( $filtering && 'Role' == $column ) {
                    echo '<div class="filterModal" style="display:none;"><select class="filterModalSelect">' . $this->role_select_dropdown() . '</select></div>';
                    echo '<span class="filter-icons">
                        <a class="filter"><span class="dashicons dashicons-filter"></span></a>
                    </span>';
                }
                echo '</th>';
            }
        }

        private function role_select_dropdown() {
            ob_start();
            $roles = new WP_Roles();
            $roles_names_array = $roles->get_names();
            echo '<option disabled selected>-- select role --</option>';
            foreach( $roles_names_array as $role ) {
                echo '<option value=' . lcfirst($role) . '>' . $role .'</option>';
            }
            return ob_get_clean();
        }

        private function render_table_rows() {
            $data = array_slice($this->data, 0, 10);
            foreach ($data as $row) {
                $row = apply_filters('custom_data_table_row', $row, $this->table_id);

                echo '<tr>';

                foreach ($row as $cell) {
                    $cell = apply_filters('custom_data_table_cell', $cell, $this->table_id);
                    echo '<td>' . esc_html($cell) . '</td>';
                }
                echo '</tr>';
            }
        }

        public function render_shortcode( $atts ) {
            
            shortcode_atts( array(
                'admin_only_message' => 'Only admins are allowed to see this content',
            ), $atts, $this->table_id );
            
            if ( is_user_logged_in() && !in_array('administrator', wp_get_current_user()->roles ) ) {
                return '<p>' . esc_html($atts['admin_only_message']) . '</p>';
            }
            
            ob_start();
            ?>
            <div class='custom-data-table' id='<?php echo "custom-data-table__{$this->table_id}" ?>' >
                <?php $this->render(); ?>
            </div>
            <?php
            return ob_get_clean();
        }

        public function handle_pagination_ajax_cb() {
            //get which page information & context
            // $this->data = [];
            
            $formData = $_POST['data'];

            if ( !isset($formData['nonce']) || !wp_verify_nonce($formData['nonce'], 'cdt_page_data_action') ) {
                wp_send_json_error(['message' => __('Nonce validation failed!')]);
                return;
            }
            

            $pageNumber = isset($formData['page']) ? $formData['page'] : 1;
            $this->data = array_slice($this->data, ($pageNumber - 1 ) * 10); //value 10 is hardcoded as of now!

            ob_start();
            $this->render_table_rows();
            $data = ob_get_clean();

            wp_send_json_success($data);
            wp_die();
        }
    }
}