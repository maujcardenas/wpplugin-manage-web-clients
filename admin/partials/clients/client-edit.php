<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

$clients_model = new TSWP_Client();
$client = null;
$is_edit = isset($_GET['action']) && $_GET['action'] === 'edit';
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_client'])) {
    if (!wp_verify_nonce($_POST['tswp_client_nonce'], 'tswp_save_client')) {
        wp_die('Security check failed');
    }

    $client_data = array(
        'first_name' => sanitize_text_field($_POST['first_name']),
        'last_name' => sanitize_text_field($_POST['last_name']),
        'email' => sanitize_email($_POST['email']),
        'cell_number' => sanitize_text_field($_POST['cell_number'])
    );

    if ($is_edit) {
        $client_id = intval($_GET['id']);
        $result = $clients_model->update($client_id, $client_data);
        if ($result) {
            $message = 'Client updated successfully.';
            $client = $clients_model->get($client_id); // Refresh client data
        } else {
            $message = 'Error updating client.';
        }
    } else {
        $result = $clients_model->create($client_data);
        if ($result) {
            wp_redirect(admin_url('admin.php?page=tswp-manage-web-clients-clients&message=created'));
            exit;
        } else {
            $message = 'Error creating client.';
        }
    }
}

// Get client data if editing
if ($is_edit && isset($_GET['id'])) {
    $client = $clients_model->get(intval($_GET['id']));
    if (!$client) {
        wp_die('Client not found');
    }
}

// Show message if redirected after creation
if (isset($_GET['message']) && $_GET['message'] === 'created') {
    $message = 'Client created successfully.';
}
?>

<div class="wrap tswp-admin">
    <?php if ($message): ?>
        <div class="notice notice-<?php echo strpos($message, 'Error') !== false ? 'error' : 'success'; ?> is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <h1 class="wp-heading-inline">
        <?php echo $is_edit ? 'Edit Client' : 'Add New Client'; ?>
    </h1>
    
    <form method="post" action="" class="tswp-form">
        <?php wp_nonce_field('tswp_save_client', 'tswp_client_nonce'); ?>
        
        <table class="form-table">
            <tr>
                <th scope="row">
                    <label for="first_name">First Name</label>
                </th>
                <td>
                    <input type="text" 
                           name="first_name" 
                           id="first_name" 
                           class="regular-text" 
                           value="<?php echo esc_attr($client ? $client->first_name : ''); ?>" 
                           required />
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="last_name">Last Name</label>
                </th>
                <td>
                    <input type="text" 
                           name="last_name" 
                           id="last_name" 
                           class="regular-text" 
                           value="<?php echo esc_attr($client ? $client->last_name : ''); ?>" 
                           required />
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="email">Email</label>
                </th>
                <td>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           class="regular-text" 
                           value="<?php echo esc_attr($client ? $client->email : ''); ?>" 
                           required />
                </td>
            </tr>
            
            <tr>
                <th scope="row">
                    <label for="cell_number">Cell Number</label>
                </th>
                <td>
                    <input type="tel" 
                           name="cell_number" 
                           id="cell_number" 
                           class="regular-text" 
                           value="<?php echo esc_attr($client ? $client->cell_number : ''); ?>" 
                           required />
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" 
                   name="submit_client" 
                   class="button button-primary" 
                   value="<?php echo $is_edit ? 'Update Client' : 'Add Client'; ?>" />
            
            <a href="<?php echo admin_url('admin.php?page=tswp-manage-web-clients-clients'); ?>" 
               class="button">Cancel</a>
        </p>
    </form>

    <?php if ($is_edit): ?>
        <!-- Display related information if editing -->
        <?php $client_info = $clients_model->get_full_info($client->id); ?>
        
        <?php if (!empty($client_info->domains)): ?>
            <div class="tswp-related-info">
                <h2>Domains</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Domain Name</th>
                            <th>Expiration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client_info->domains as $domain): ?>
                            <tr>
                                <td><?php echo esc_html($domain->domain_name); ?></td>
                                <td><?php echo esc_html($domain->expiration_date); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($client_info->applications)): ?>
            <div class="tswp-related-info">
                <h2>Applications</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Application Name</th>
                            <th>Hosting Plan</th>
                            <th>Expiration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client_info->applications as $app): ?>
                            <tr>
                                <td><?php echo esc_html($app->application_name); ?></td>
                                <td><?php echo esc_html($app->hosting_plan); ?></td>
                                <td><?php echo esc_html($app->expiration_date); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($client_info->emails)): ?>
            <div class="tswp-related-info">
                <h2>Email Accounts</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Email Address</th>
                            <th>Plan</th>
                            <th>Expiration Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client_info->emails as $email): ?>
                            <tr>
                                <td><?php echo esc_html($email->email_address); ?></td>
                                <td><?php echo esc_html($email->email_plan); ?></td>
                                <td><?php echo esc_html($email->expiration_date); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <?php if (!empty($client_info->payments)): ?>
            <div class="tswp-related-info">
                <h2>Payment History</h2>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Service</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($client_info->payments as $payment): ?>
                            <tr>
                                <td><?php echo esc_html($payment->payment_date); ?></td>
                                <td><?php echo esc_html($payment->service_id); ?></td>
                                <td><?php echo esc_html($payment->status); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>