<?php
/**
 * Domain model class
 */
class TSWP_Domain {
    private $db;
    private $table = 'domains';

    public function __construct() {
        $this->db = new TSWP_Database();
    }

    /**
     * Create a new domain
     */
    public function create($data) {
        if (!$this->validate($data)) {
            return false;
        }

        return $this->db->insert($this->table, array(
            'client_id' => absint($data['client_id']),
            'domain_name' => sanitize_text_field($data['domain_name']),
            'expiration_date' => sanitize_text_field($data['expiration_date']),
            'application_id' => isset($data['application_id']) ? absint($data['application_id']) : null
        ));
    }

    /**
     * Update an existing domain
     */
    public function update($id, $data) {
        if (!$this->validate($data)) {
            return false;
        }

        return $this->db->update(
            $this->table,
            array(
                'client_id' => absint($data['client_id']),
                'domain_name' => sanitize_text_field($data['domain_name']),
                'expiration_date' => sanitize_text_field($data['expiration_date']),
                'application_id' => isset($data['application_id']) ? absint($data['application_id']) : null
            ),
            array('id' => $id)
        );
    }

    /**
     * Delete a domain
     */
    public function delete($id) {
        return $this->db->delete($this->table, array('id' => $id));
    }

    /**
     * Get a single domain by ID
     */
    public function get($id) {
        $results = $this->db->get($this->table, array('id' => $id));
        return $results ? $results[0] : false;
    }

    /**
     * Get domains by client ID
     */
    public function get_by_client($client_id, $page = 1, $per_page = 10) {
        return $this->db->get_paged($this->table, $page, $per_page, array('client_id' => $client_id));
    }

    /**
     * Get all domains with optional pagination
     */
    public function get_all($page = 1, $per_page = 10) {
        return $this->db->get_paged($this->table, $page, $per_page);
    }

    /**
     * Get total number of domains
     */
    public function count() {
        return $this->db->count($this->table);
    }

    /**
     * Get domains expiring soon
     */
    public function get_expiring_soon($days = 30) {
        global $wpdb;
        $table_name = $this->db->get_table($this->table);
        
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name 
            WHERE expiration_date BETWEEN CURDATE() 
            AND DATE_ADD(CURDATE(), INTERVAL %d DAY)
            ORDER BY expiration_date ASC",
            $days
        );
        
        return $wpdb->get_results($sql);
    }

    /**
     * Search domains
     */
    public function search($term) {
        global $wpdb;
        $table_name = $this->db->get_table($this->table);
        
        $sql = $wpdb->prepare(
            "SELECT * FROM $table_name WHERE domain_name LIKE %s",
            '%' . $wpdb->esc_like($term) . '%'
        );
        
        return $wpdb->get_results($sql);
    }

    /**
     * Validate domain data
     */
    private function validate($data) {
        if (empty($data['client_id']) || empty($data['domain_name']) || 
            empty($data['expiration_date'])) {
            return false;
        }

        // Validate domain name format
        if (!preg_match('/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,}$/', $data['domain_name'])) {
            return false;
        }

        // Validate date format (YYYY-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $data['expiration_date'])) {
            return false;
        }

        // Validate client exists
        $client = new TSWP_Client();
        if (!$client->get($data['client_id'])) {
            return false;
        }

        // Validate application exists if provided
        if (!empty($data['application_id'])) {
            require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-application.php';
            $application = new TSWP_Application();
            if (!$application->get($data['application_id'])) {
                return false;
            }
        }

        return true;
    }
}