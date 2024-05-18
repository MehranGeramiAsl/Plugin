<?php
// Ensure this file is called only from within WordPress.
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Hook into WooCommerce order status change
add_action('woocommerce_order_status_changed', 'custom_order_status_sms_send_sms_notification', 10, 4);

function custom_order_status_sms_send_sms_notification($order_id, $old_status, $new_status, $order){
    // Get plugin settings
    $options = get_option('custom_order_status_sms_settings');
    if (!isset($options[$new_status]) || $options[$new_status] != 1) {
        return; // SMS notifications not enabled for this order status
    }

    // Get customer information
    $customer_id = $order->get_customer_id();
    $customer = new WC_Customer($customer_id);
    $customer_phone = $customer->get_billing_phone();

    // Get order details
    $order_number = $order->get_order_number();
    $order_total = $order->get_total();

    // Prepare SMS content
    $sms_content = "Dear {$customer->get_first_name()}, your order {$order_number} status has changed to {$new_status}. Total amount: {$order_total}";

    // Send SMS using SMS panel API
    $api_url = 'YOUR_SMS_PANEL_API_URL';
    $api_username = 'YOUR_API_USERNAME';
    $api_password = 'YOUR_API_PASSWORD';

    $sms_data = array(
        'to' => $customer_phone,
        'message' => $sms_content,
        // Add other required parameters for your SMS panel API
    );

    // Send SMS using cURL
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($sms_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERPWD, "$api_username:$api_password");
    $response = curl_exec($ch);
    curl_close($ch);

    // Log the SMS response
    error_log("SMS response: $response");
}
