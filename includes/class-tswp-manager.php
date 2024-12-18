<?php
/**
 * The core plugin class
 */
class TSWP_Manager {
    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     */
    protected $loader;

    /**
     * The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     */
    public function __construct() {
        $this->version = TSWP_VERSION;
        $this->load_dependencies();
        $this->define_admin_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     */
    private function load_dependencies() {
        require_once TSWP_PLUGIN_DIR . 'includes/class-tswp-loader.php';
        require_once TSWP_PLUGIN_DIR . 'includes/class-tswp-database.php';
        require_once TSWP_PLUGIN_DIR . 'admin/class-tswp-admin.php';

        // Load model classes
        require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-client.php';
        require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-domain.php';
        require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-application.php';
        require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-email.php';
        require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-payment.php';
        require_once TSWP_PLUGIN_DIR . 'includes/models/class-tswp-service.php';

        $this->loader = new TSWP_Loader();
    }

    /**
     * Register all of the hooks related to the admin area functionality
     */
    private function define_admin_hooks() {
        $plugin_admin = new TSWP_Admin($this->get_version());

        // Add menu items
        $this->loader->add_action('admin_menu', $plugin_admin, 'add_plugin_admin_menu');
        
        // Add admin assets
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    /**
     * Run the loader to execute all the hooks with WordPress.
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }
}