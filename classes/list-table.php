<?php

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Auto_Login_Link_Table extends WP_List_Table
{

    function __construct() {
        parent::__construct([
            'singular' => __('auto login link', Auto_Login_Link::LANGUAGE_DOMAIN),
            'plural' => __('auto login links', Auto_Login_Link::LANGUAGE_DOMAIN),
            'ajax' => FALSE
        ]);

        add_action('admin_head', [$this, 'admin_header']);
    }
    
    function no_items() {
        _e('No link available');
    }
    
    public function get_columns() {
		return [
            'cb' => '<input type="checkbox" />',
            'id' => __('ID', Auto_Login_Link::LANGUAGE_DOMAIN),
			'url' => __('URL', Auto_Login_Link::LANGUAGE_DOMAIN),
			'user' => __('User', Auto_Login_Link::LANGUAGE_DOMAIN),
        ];
    }
    
    function get_bulk_actions() {
        $actions = [
            'delete' => 'Delete'
        ];
        return $actions;
    }
    
    function column_cb($item) {
        return sprintf('<input type="checkbox" name="item[]" value="%s" />', $item['id']);
    }
    
    function column_id($item) {
        return $item['id'];
    }
    
    function column_url($item) {
        $edit_url = admin_url('admin.php?page=' . Auto_Login_Link::PLUGIN_SLUG . '&tab=edit&id=' . $item['id']);
        $delete_url = admin_url('admin.php?page=' . Auto_Login_Link::PLUGIN_SLUG . '&tab=delete&id=' . $item['id']);
        return site_url() . '/' . $item['url'] . '<div class="row-actions"><span class="edit"><a href="' . $edit_url . '">' . __('Edit', Auto_Login_Link::LANGUAGE_DOMAIN) . '</a> | </span>
        <span class="trash"><a href="' . $delete_url . '" class="submitdelete">' . __('Delete', Auto_Login_Link::LANGUAGE_DOMAIN) . '</a></span>
        </div>';
    }
    
    function column_user($item) {
        if ($item['user_id']) {
            $user = get_user_by('id', $item['user_id']);
            return $user->data->user_login . ' (' . $user->data->user_email . ')';
        }
    }
    
    function prepare_items() {
        $columns               = $this->get_columns();
        $hidden                = [];
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = [
            $columns,
            $hidden,
            $sortable
        ];
        
        $per_page = 20;
		$offset = ($this->get_pagenum() - 1) * $per_page;
        
        $model = new Auto_Login_Link_Model();
		$data = $model->fetch_all($per_page, $offset, isset($_GET['orderby']) ? esc_attr($_GET['orderby']) : null, isset($_GET['order']) ? esc_attr($_GET['order']) : null);
        
        $this->set_pagination_args([
            'total_items' => $model->count_all(),
            'per_page' => $per_page
        ]);

        $this->items = $data;
    }
    
}