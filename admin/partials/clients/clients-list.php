<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Initialize the clients model
$clients_model = new TSWP_Client();

// Handle bulk actions
if (isset($_POST['action']) && isset($_POST['client'])) {
    if ($_POST['action'] === 'delete' && check_admin_referer('tswp_clients_actions', 'tswp_nonce')) {
        foreach ($_POST['client'] as $client_id) {
            $clients_model->delete($client_id);
        }
        echo '<div class="notice notice-success"><p>Selected clients deleted successfully.</p></div>';
    }
}

// Get current page and items per page
$current_page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
$items_per_page = 10;

// Get clients for current page
$clients = $clients_model->get_all($current_page, $items_per_page);
$total_clients = $clients_model->count();
$total_pages = ceil($total_clients / $items_per_page);
?>

<div class="wrap tswp-admin">
    <h1 class="wp-heading-inline">Clients</h1>
    <a href="<?php echo admin_url('admin.php?page=tswp-manage-web-clients-clients&action=new'); ?>" class="page-title-action">Add New Client</a>
    
    <?php if (!empty($clients)): ?>
        <form method="post" action="">
            <?php wp_nonce_field('tswp_clients_actions', 'tswp_nonce'); ?>
            
            <div class="tablenav top">
                <div class="alignleft actions bulkactions">
                    <select name="action">
                        <option value="-1">Bulk Actions</option>
                        <option value="delete">Delete</option>
                    </select>
                    <input type="submit" class="button action" value="Apply">
                </div>
                
                <!-- Pagination Top -->
                <div class="tablenav-pages">
                    <?php if ($total_pages > 1): ?>
                        <span class="pagination-links">
                            <?php
                            echo paginate_links(array(
                                'base' => add_query_arg('paged', '%#%'),
                                'format' => '',
                                'prev_text' => __('&laquo;'),
                                'next_text' => __('&raquo;'),
                                'total' => $total_pages,
                                'current' => $current_page
                            ));
                            ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" />
                        </td>
                        <th scope="col" class="manage-column">Name</th>
                        <th scope="col" class="manage-column">Email</th>
                        <th scope="col" class="manage-column">Phone</th>
                        <th scope="col" class="manage-column">Domains</th>
                        <th scope="col" class="manage-column">Applications</th>
                        <th scope="col" class="manage-column">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($clients as $client): ?>
                        <?php $client_info = $clients_model->get_full_info($client->id); ?>
                        <tr>
                            <th scope="row" class="check-column">
                                <input type="checkbox" name="client[]" value="<?php echo $client->id; ?>" />
                            </th>
                            <td>
                                <strong>
                                    <a href="<?php echo admin_url('admin.php?page=tswp-manage-web-clients-clients&action=edit&id=' . $client->id); ?>">
                                        <?php echo esc_html($client->first_name . ' ' . $client->last_name); ?>
                                    </a>
                                </strong>
                            </td>
                            <td><?php echo esc_html($client->email); ?></td>
                            <td><?php echo esc_html($client->cell_number); ?></td>
                            <td><?php echo count($client_info->domains ?? []); ?></td>
                            <td><?php echo count($client_info->applications ?? []); ?></td>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=tswp-manage-web-clients-clients&action=edit&id=' . $client->id); ?>" 
                                   class="button button-small">Edit</a>
                                <a href="<?php echo admin_url('admin.php?page=tswp-manage-web-clients-client-info&client=' . $client->id); ?>" 
                                   class="button button-small">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

                <tfoot>
                    <tr>
                        <td class="manage-column column-cb check-column">
                            <input type="checkbox" />
                        </td>
                        <th scope="col" class="manage-column">Name</th>
                        <th scope="col" class="manage-column">Email</th>
                        <th scope="col" class="manage-column">Phone</th>
                        <th scope="col" class="manage-column">Domains</th>
                        <th scope="col" class="manage-column">Applications</th>
                        <th scope="col" class="manage-column">Actions</th>
                    </tr>
                </tfoot>
            </table>
        </form>
    <?php else: ?>
        <div class="no-items">
            <p>No clients found. <a href="<?php echo admin_url('admin.php?page=tswp-manage-web-clients-clients&action=new'); ?>">Add your first client</a></p>
        </div>
    <?php endif; ?>
</div>