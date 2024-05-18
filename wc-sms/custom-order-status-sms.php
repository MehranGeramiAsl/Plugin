<?php
/*
Plugin Name: Custom Order Status SMS
Plugin URI: http://yourwebsite.com
Description: A plugin to manage SMS notifications for custom order statuses.
Version: 1.0
Author: Your Name
Author URI: http://yourwebsite.com
License: GPL2
*/

// Create custom menu item for plugin
add_action('admin_menu', 'custom_order_status_sms_menu');

function custom_order_status_sms_menu() {
    add_menu_page(
        'Order Status SMS', // Page title
        'Order Status SMS', // Menu title
        'manage_options', // Capability required
        'custom_order_status_sms_settings', // Menu slug
        'custom_order_status_sms_settings_page', // Callback function
        'dashicons-email' // Icon
    );
}

// Display settings page content
function custom_order_status_sms_settings_page() {
    ?>
    <div class="wrap">
        <h2>Order Status SMS Settings</h2>
        <form method="post" action="options.php">
            <?php settings_fields('custom_order_status_sms_settings_group'); ?>
            <?php do_settings_sections('custom_order_status_sms_settings_group'); ?>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings
add_action('admin_init', 'custom_order_status_sms_register_settings');

function custom_order_status_sms_register_settings() {
    // Register settings group
    register_setting('custom_order_status_sms_settings_group', 'custom_order_status_sms_settings');

    // Add settings section
    add_settings_section('custom_order_status_sms_section', 'Order Status Settings', 'custom_order_status_sms_section_callback', 'custom_order_status_sms_settings_group');

    // Add settings fields
    $order_statuses = wc_get_order_statuses();
    foreach ($order_statuses as $status => $label) {
        add_settings_field(
            "custom_order_status_sms_$status",
            $label,
            'custom_order_status_sms_field_callback',
            'custom_order_status_sms_settings_group',
            'custom_order_status_sms_section',
            array(
                'status' => $status,
                'label' => $label
            )
        );
    }
}

// Callback function for settings section
function custom_order_status_sms_section_callback() {
    echo 'Configure SMS settings for each order status';
}

// Callback function for settings fields
function custom_order_status_sms_field_callback($args) {
    $options = get_option('custom_order_status_sms_settings');
    $status = $args['status'];
    $label = $args['label'];
    $checked = isset($options[$status]) ? checked($options[$status], 1, false) : '';
    ?>
    <label for="custom_order_status_sms_<?php echo $status; ?>">
        <input type="checkbox" id="custom_order_status_sms_<?php echo $status; ?>" name="custom_order_status_sms_settings[<?php echo $status; ?>]" value="1" <?php echo $checked; ?>>
        Enable SMS for <?php echo $label; ?>
    </label>
    <?php
}
