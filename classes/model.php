<?php

class Auto_Login_Link_Model
{

    public function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'auto_login_link';
    }

    public function fetch_all($limit = NULL, $offset = 0, $order = NULL, $dir = 'ASC') {
		global $wpdb;

		$query = "SELECT * FROM $this->table ";

		if (isset($order)) {
			$query .= " ORDER BY $order $dir";
        }

		if (isset($limit)) {
			$query .= " LIMIT $offset,$limit";
        }

		return ($wpdb->get_results($query, ARRAY_A));
    }

    public function count_all() {
        global $wpdb;

        $query = "SELECT COUNT(*) AS total FROM $this->table ";
        return $wpdb->get_row($query)->total;
    }

    public function create($args) {
        global $wpdb;

        $data = $this->_format_post_data($args);
        return $wpdb->insert($this->table, $data);
    }

    public function update($id, $args) {
        global $wpdb;

        $old_email_data = $this->get(['id' => $id, 'single' => TRUE]);
        $data = $this->_format_post_data($args);

        return $wpdb->update($this->table, $data, ['id' => $id]);
    }

    public function delete($id) {
        global $wpdb;

        if (is_array($id)) {
            $where = ' id IN (' . implode(',', $id). ')';
        } else {
            $where = ' id = ' . $id;
        }

        $wpdb->query("DELETE FROM $this->table WHERE $where");
    }
    
    public function get($args) {
        global $wpdb;

        $order = '';
        $where = $join = $column = [];

        // search by id
        if (isset($args['id'])) {
            $where[] = $wpdb->prepare("id = %d", $args['id']);
        }

        // search by url
        if (isset($args['url'])) {
            $where[] = $wpdb->prepare("url = %s", $args['url']);
        }

        $where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';
        $join = !empty($join) ? implode(' ', $join) : '';
        $column = !empty($column) ? implode(',', $column) : '*';

        $query = "SELECT $column FROM $this->table $join $where $order";

        if (isset($args['single'])) {
            return $wpdb->get_row($query);
        } else {
            return $wpdb->get_results($query);
        }
    }

    private function _format_post_data($args) {
        $args = array_map('stripslashes_deep', $args);
        $data = [];
        $data['url'] = esc_attr($args['url']);
        $data['user_id'] = isset($args['user_id']) ? $args['user_id'] : 0;
        return $data;
    }
}