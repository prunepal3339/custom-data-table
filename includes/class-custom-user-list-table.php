<?php

if( !defined( 'ABSPATH') ) {
    exit;
}
require_once( CUSTOM_DATA_TABLE_DIR . 'includes/class-custom-data-table.php');
if ( !class_exists('Custom_User_List_Table')) {
    class Custom_User_List_Table extends Custom_Data_Table {
        public function __construct($table_id="custom_user_list_table") {
            $columns = ['ID', 'Username', 'Display name', 'Role'];

            $data = $this->get_user_data();

            $options = [
                'paging' => true,
                'searching' => false,
                'ordering' => true,
                'responsive' => true,
            ];
            parent::__construct(
                $table_id,
                $columns,
                $data,
                $options,
            );
        }
        private function get_user_data(): array {
            $users = get_users();
            $data = [];

            foreach ($users as $user) {
                $data[] = [
                    $user->ID,
                    $user->user_login,
                    $user->display_name,
                    implode(',', $user->roles),
                ];
            }
            return $data;
        }
    }
}