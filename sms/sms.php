<?php
/*
Plugin Name: Order Status SMS Notifications
Plugin URI: http://yourwebsite.com
Description: A plugin to send SMS notifications based on order status in WooCommerce.
Version: 1.0
Author: Your Name
Author URI: http://yourwebsite.com
License: GPL2
*/

// Add admin menu page for settings
add_action('admin_menu', 'order_status_sms_notifications_menu');

function order_status_sms_notifications_menu() {
    add_menu_page(
        'SMS Notifications Settings',
        'SMS Notifications',
        'manage_options',
        'order-status-sms-notifications',
        'order_status_sms_notifications_page'
    );
}

// Display settings page content
function order_status_sms_notifications_page() {
    ?>
    <div class="wrap">
        <h2>SMS Notifications Settings</h2>
        <form method="post" action="options.php">
            <?php
            settings_fields('order_status_sms_notifications_options');
            do_settings_sections('order-status-sms-notifications');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Register settings and sections
add_action('admin_init', 'order_status_sms_notifications_init');

function order_status_sms_notifications_init() {
    register_setting('order_status_sms_notifications_options', 'order_status_sms_notifications_settings');

    add_settings_section(
        'order_status_sms_notifications_main',
        'Order Status SMS Notifications',
        'order_status_sms_notifications_section_callback',
        'order-status-sms-notifications'
    );

    // Get all order statuses
    $order_statuses = wc_get_order_statuses();

    // Add fields for each order status
    foreach ($order_statuses as $status_key => $status_label) {
        add_settings_field(
            'order_status_sms_notifications_' . $status_key,
            $status_label,
            'order_status_sms_notifications_field_callback',
            'order-status-sms-notifications',
            'order_status_sms_notifications_main',
            array(
                'status_key' => $status_key,
                'status_label' => $status_label
            )
        );
    }
}

// Callback for main section
function order_status_sms_notifications_section_callback() {
    echo '<p>Configure SMS notifications for each order status.</p>';
}

// Callback for each field
function order_status_sms_notifications_field_callback($args) {
    $options = get_option('order_status_sms_notifications_settings');
    $status_key = $args['status_key'];
    $status_label = $args['status_label'];
    $status_settings = isset($options[$status_key]) ? $options[$status_key] : array(
        'template_name' => '',
        'token1' => '',
        'token2' => '',
        'token3' => '',
        'sms_enabled' => false
    );
    ?>
    <label for="template_name_<?php echo $status_key; ?>">Template Name:</label>
    <input type="text" id="template_name_<?php echo $status_key; ?>" name="order_status_sms_notifications_settings[<?php echo $status_key; ?>][template_name]" value="<?php echo $status_settings['template_name']; ?>"><br>
    <label for="token1_<?php echo $status_key; ?>">Token 1:</label>
    <input type="text" id="token1_<?php echo $status_key; ?>" name="order_status_sms_notifications_settings[<?php echo $status_key; ?>][token1]" value="<?php echo $status_settings['token1']; ?>"><br>
    <label for="token2_<?php echo $status_key; ?>">Token 2:</label>
    <input type="text" id="token2_<?php echo $status_key; ?>" name="order_status_sms_notifications_settings[<?php echo $status_key; ?>][token2]" value="<?php echo $status_settings['token2']; ?>"><br>
    <label for="token3_<?php echo $status_key; ?>">Token 3:</label>
    <input type="text" id="token3_<?php echo $status_key; ?>" name="order_status_sms_notifications_settings[<?php echo $status_key; ?>][token3]" value="<?php echo $status_settings['token3']; ?>"><br>
    <label for="sms_enabled_<?php echo $status_key; ?>"><input type="checkbox" id="sms_enabled_<?php echo $status_key; ?>" name="order_status_sms_notifications_settings[<?php echo $status_key; ?>][sms_enabled]" <?php checked($status_settings['sms_enabled'], true); ?>> Enable SMS</label>
    <?php
}

// Hook into order status change
// add_action('woocommerce_order_status_changed', 'send_sms_notification', 10, 4);

// function send_sms_notification($order_id, $old_status, $new_status, $order) {
//     $options = get_option('order_status_sms_notifications_settings');
//     $status_settings = isset($options[$new_status]) ? $options[$new_status] : array(
//         'template_name' => '',
//         'token1' => '',
//         'token2' => '',
//         'token3' => '',
//         'sms_enabled' => false,
//     );

//     if ($status_settings['sms_enabled']) {
//         $customer_phone = $order->get_billing_phone();
//         $template_name = $status_settings['template_name'];
//         $token1 = $status_settings['token1'];
//         $token2 = $status_settings['token2'];
//         $token3 = $status_settings['token3'];
//         // Send SMS using provided parameters
//         // Example: send_sms($customer_phone, $template_name, $token1, $token2, $token3);
//     }
// }
