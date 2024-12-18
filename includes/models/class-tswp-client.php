<?php
/**
 * Client model class
 */
class TSWP_Client {
    private $db;
    private $table = 'clients';

    public function __construct() {
        $this->db = new TSWP_Database();
    }

    /**
     * Create a new client
     */
    public function create($data) {
        if (!$this->validate($data)) {
            return false;
        }

        return $this->db->insert($this->table, array(
            'first_name' => sanitize_text_field($data['first_name']),
            'last_name' => sanitize_text_field($data['last_name']),
            'email' => sanitize_email($data['email']),
            'cell_number' => sanitize_text_field($data['cell_number'])
        ));
    }

    /**
     * Update an existing client
     */
    public function update($id, $data) {
        if (!$this->validate($data)) {
            return false;
        }

        return $this->db->update(
            $this->table,
            array(
                'first_name' => sanitize_text_field($data['first_name']),
                'last_name' => sanitize_text_field($data['last_name']),
                'email' => sanitize_email($data['email']),
                'cell_number' => sanitize_text_field($data['cell_number'])
            ),
            array('id' => $id)
        );
    }

    /**
     * Delete a client
     */
    public function delete($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    /**
     * Get a single client by ID
     */
    public function get($id) {
        $results = $this->db->get($this->table, array('id' => $id));
        return $results ? $results[0] : false;
    }

    /**
     * Get all clients with optional pagination
     */
    public function get_all($page = 1, $per_page = 10) {
        return $this->db->get_paged($this->table, $page, $per_page);
    }

    /**
     * Get total number of clients
     */
    public function count() {
        return $this->db->count($this->table);
    }

    /**
     * Get client's full information including related data
     */
    public function get_full_info($id) {
        return $this->db->get_client_full_info($id);
    }

    /**
     * Search clients
     */
    public function search($term) {
        global $wpdb;
        $table_name = $this->db->get_table($this->table);
        
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE first_name LIKE %s 
            OR last_name LIKE %s 
            OR email LIKE %s 
            OR cell_number LIKE %s",
            '%' . $wpdb->esc_like($term) . '%',
            '%' . $wpdb->esc_like($term) . '%',
            '%' . $wpdb->esc_like($term) . '%',
            '%' . $wpdb->esc_like($term) . '%'
        );
        
        return $wpdb->get_results($sql);
    }

    /**
     * Validate client data
     */
    private function validate($data) {
        if (empty($data['first_name']) || empty($data['last_name']) || 
            empty($data['email']) || empty($data['cell_number'])) {
            return false;
        }

        if (!is_email($data['email'])) {
            return false;
        }

        // Basic phone number validation (can be enhanced based on requirements)
        if (!preg_match('/^[0-9+\-\(\) ]{6,20}$/', $data['cell_number'])) {
            return false;
        }

        return true;
    }
}