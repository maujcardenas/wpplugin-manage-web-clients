<?php
/**
 * Handle all database operations
 */
class TSWP_Database {
    private $wpdb;
    private $tables;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        
        // Define table names
        $this->tables = array(
            'clients'       => $wpdb->prefix . 'tswp_clients',
            'domains'       => $wpdb->prefix . 'tswp_domains',
            'applications'  => $wpdb->prefix . 'tswp_applications',
            'emails'        => $wpdb->prefix . 'tswp_emails',
            'payments'      => $wpdb->prefix . 'tswp_payments',
            'services'      => $wpdb->prefix . 'tswp_services'
        );
    }

    /**
     * Get table name
     */
    public function get_table($table) {
        return isset($this->tables[$table]) ? $this->tables[$table] : false;
    }

    /**
     * Generic insert method
     */
    public function insert($table, $data) {
        $table_name = $this->get_table($table);
        if (!$table_name) return false;

        return $this->wpdb->insert($table_name, $data);
    }

    /**
     * Generic update method
     */
    public function update($table, $data, $where) {
        $table_name = $this->get_table($table);
        if (!$table_name) return false;

        return $this->wpdb->update($table_name, $data, $where);
    }

    /**
     * Generic delete method
     */
    public function delete($table, $where) {
        $table_name = $this->get_table($table);
        if (!$table_name) return false;

        return $this->wpdb->delete($table_name, $where);
    }

    /**
     * Generic get method with optional where clause
     */
    public function get($table, $where = null, $order_by = null, $limit = null) {
        $table_name = $this->get_table($table);
        if (!$table_name) return false;

        $sql = "SELECT * FROM $table_name";
        
        if ($where) {
            $sql .= " WHERE ";
            foreach ($where as $key => $value) {
                $sql .= $key . " = '" . esc_sql($value) . "' AND ";
            }
            $sql = rtrim($sql, "AND ");
        }

        if ($order_by) {
            $sql .= " ORDER BY $order_by";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->wpdb->get_results($sql);
    }

    /**
     * Get client with all related data
     */
    public function get_client_full_info($client_id) {
        $client = $this->get('clients', array('id' => $client_id));
        if (!$client) return false;

        $client = $client[0];
        $client->domains = $this->get('domains', array('client_id' => $client_id));
        $client->applications = $this->get('applications', array('client_id' => $client_id));
        $client->emails = $this->get('emails', array('client_id' => $client_id));
        $client->payments = $this->get('payments', array('client_id' => $client_id));

        return $client;
    }

    /**
     * Get items with pagination
     */
    public function get_paged($table, $page = 1, $per_page = 10, $where = null) {
        $offset = ($page - 1) * $per_page;
        return $this->get($table, $where, null, "$offset, $per_page");
    }

    /**
     * Count total items
     */
    public function count($table, $where = null) {
        $table_name = $this->get_table($table);
        if (!$table_name) return 0;

        $sql = "SELECT COUNT(*) FROM $table_name";
        
        if ($where) {
            $sql .= " WHERE ";
            foreach ($where as $key => $value) {
                $sql .= $key . " = '" . esc_sql($value) . "' AND ";
            }
            $sql = rtrim($sql, "AND ");
        }

        return $this->wpdb->get_var($sql);
    }
}