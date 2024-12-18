<?php
/**
 * Fired during plugin activation
 */
class TSWP_Activator {
    /**
     * Create the necessary database tables during plugin activation
     */
    public static function activate() {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $wpdb->get_charset_collate();

        // Create clients table
        $clients_table = $wpdb->prefix . 'tswp_clients';
        $sql = "CREATE TABLE $clients_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            first_name varchar(50) NOT NULL,
            last_name varchar(50) NOT NULL,
            email varchar(100) NOT NULL,
            cell_number varchar(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Create domains table
        $domains_table = $wpdb->prefix . 'tswp_domains';
        $sql = "CREATE TABLE $domains_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            client_id mediumint(9) NOT NULL,
            domain_name varchar(255) NOT NULL,
            expiration_date date NOT NULL,
            application_id mediumint(9),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY client_id (client_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Create applications table
        $applications_table = $wpdb->prefix . 'tswp_applications';
        $sql = "CREATE TABLE $applications_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            application_name varchar(100) NOT NULL,
            client_id mediumint(9) NOT NULL,
            hosting_plan ENUM('hosting-2gb', 'hosting-4gb', 'hosting-8gb', 'hosting-12gb') NOT NULL,
            expiration_date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY client_id (client_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Create emails table
        $emails_table = $wpdb->prefix . 'tswp_emails';
        $sql = "CREATE TABLE $emails_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            email_address varchar(100) NOT NULL,
            domain_id mediumint(9) NOT NULL,
            client_id mediumint(9) NOT NULL,
            email_plan ENUM('standard 1-3', 'standard 4-10', 'standard 11-50', 'premium 10gb', 'premium 50gb', 'premium 100gb') NOT NULL,
            expiration_date date NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY domain_id (domain_id),
            KEY client_id (client_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Create services table
        $services_table = $wpdb->prefix . 'tswp_services';
        $sql = "CREATE TABLE $services_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(100) NOT NULL,
            description text NOT NULL,
            price decimal(10,2) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";
        dbDelta($sql);

        // Create payments table
        $payments_table = $wpdb->prefix . 'tswp_payments';
        $sql = "CREATE TABLE $payments_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            client_id mediumint(9) NOT NULL,
            service_id mediumint(9) NOT NULL,
            payment_date datetime NOT NULL,
            status ENUM('processing', 'completed') NOT NULL DEFAULT 'processing',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY client_id (client_id),
            KEY service_id (service_id)
        ) $charset_collate;";
        dbDelta($sql);

        // Add foreign key constraints
        $wpdb->query("ALTER TABLE $domains_table 
            ADD CONSTRAINT `fk_domain_client` 
            FOREIGN KEY (client_id) REFERENCES $clients_table(id) ON DELETE CASCADE");

        $wpdb->query("ALTER TABLE $applications_table 
            ADD CONSTRAINT `fk_application_client` 
            FOREIGN KEY (client_id) REFERENCES $clients_table(id) ON DELETE CASCADE");

        $wpdb->query("ALTER TABLE $emails_table 
            ADD CONSTRAINT `fk_email_domain` 
            FOREIGN KEY (domain_id) REFERENCES $domains_table(id) ON DELETE CASCADE,
            ADD CONSTRAINT `fk_email_client` 
            FOREIGN KEY (client_id) REFERENCES $clients_table(id) ON DELETE CASCADE");

        $wpdb->query("ALTER TABLE $payments_table 
            ADD CONSTRAINT `fk_payment_client` 
            FOREIGN KEY (client_id) REFERENCES $clients_table(id) ON DELETE CASCADE,
            ADD CONSTRAINT `fk_payment_service` 
            FOREIGN KEY (service_id) REFERENCES $services_table(id) ON DELETE CASCADE");

        add_option('tswp_manage_web_clients_version', TSWP_VERSION);
    }
}