<?php
// If uninstall not called from WordPress, then exit.
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Drop custom tables
global $wpdb;

$tables = array(
    $wpdb->prefix . 'tswp_clients',
    $wpdb->prefix . 'tswp_domains',
    $wpdb->prefix . 'tswp_applications',
    $wpdb->prefix . 'tswp_emails',
    $wpdb->prefix . 'tswp_payments',
    $wpdb->prefix . 'tswp_services'
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS $table");
}

// Delete any options related to the plugin
delete_option('tswp_manage_web_clients_version');
delete_option('tswp_manage_web_clients_settings');