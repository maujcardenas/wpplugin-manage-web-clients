<?php
/**
 * The admin-specific functionality of the plugin.
 */
class TSWP_Admin {
    private $version;
    private $plugin_name;

    public function __construct($version) {
        $this->version = $version;
        $this->plugin_name = 'tswp-manage-web-clients';
    }

    /**
     * Register the stylesheets for the admin area.
     */
    public function enqueue_styles() {
        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'css/tswp-admin.css',
            array(),
            $this->version,
            'all'
        );
    }

    /**
     * Register the JavaScript for the admin area.
     */
    public function enqueue_scripts() {
        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . 'js/tswp-admin.js',
            array('jquery'),
            $this->version,
            false
        );

        // Pass ajax url to JS
        wp_localize_script($this->plugin_name, 'tswpAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tswp_nonce')
        ));
    }

    /**
     * Add plugin admin menu
     */
    public function add_plugin_admin_menu() {
        // Main menu item
        add_menu_page(
            'TSWP Client Manager', 
            'Client Manager',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_clients_page'),
            'dashicons-groups',
            30
        );

        // Submenus
        add_submenu_page(
            $this->plugin_name,
            'Dashboard',
            'Dashboard',
            'manage_options',
            $this->plugin_name,
            array($this, 'display_clients_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Clients',
            'Clients',
            'manage_options',
            $this->plugin_name . '-clients',
            array($this, 'display_clients_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Domains',
            'Domains',
            'manage_options',
            $this->plugin_name . '-domains',
            array($this, 'display_domains_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Applications',
            'Applications',
            'manage_options',
            $this->plugin_name . '-applications',
            array($this, 'display_applications_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Emails',
            'Emails',
            'manage_options',
            $this->plugin_name . '-emails',
            array($this, 'display_emails_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Services',
            'Services',
            'manage_options',
            $this->plugin_name . '-services',
            array($this, 'display_services_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Payments',
            'Payments',
            'manage_options',
            $this->plugin_name . '-payments',
            array($this, 'display_payments_page')
        );

        add_submenu_page(
            $this->plugin_name,
            'Client Information',
            'Client Information',
            'manage_options',
            $this->plugin_name . '-client-info',
            array($this, 'display_client_info_page')
        );
    }

    /**
     * Display pages
     */

    // public function display_plugin_dashboard_page() {
    //     include_once 'partials/dashboard.php';
    // }

    public function display_clients_page() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $client_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        switch ($action) {
            case 'edit':
                include_once 'partials/clients/client-edit.php';
                break;
            case 'new':
                include_once 'partials/clients/client-edit.php';
                break;
            default:
                include_once 'partials/clients/clients-list.php';
        }
    }

    public function display_domains_page() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        $domain_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        switch ($action) {
            case 'edit':
                include_once 'partials/domains/domain-edit.php';
                break;
            case 'new':
                include_once 'partials/domains/domain-edit.php';
                break;
            default:
                include_once 'partials/domains/domains-list.php';
        }
    }

    public function display_applications_page() {
        include_once 'partials/applications/applications-list.php';
    }

    public function display_emails_page() {
        include_once 'partials/emails/emails-list.php';
    }

    public function display_services_page() {
        include_once 'partials/services/services-list.php';
    }

    public function display_payments_page() {
        include_once 'partials/payments/payments-list.php';
    }

    public function display_client_info_page() {
        include_once 'partials/client-information.php';
    }

    /**
     * Ajax handlers
     */
    public function handle_ajax_request() {
        check_ajax_referer('tswp_nonce', 'nonce');

        $action = $_POST['action'];
        $response = array('success' => false);

        switch ($action) {
            case 'get_client_info':
                $client_id = intval($_POST['client_id']);
                $client = new TSWP_Client();
                $client_info = $client->get_full_info($client_id);
                if ($client_info) {
                    $response['success'] = true;
                    $response['data'] = $client_info;
                }
                break;
            // Add more ajax handlers as needed
        }

        wp_send_json($response);
    }
}