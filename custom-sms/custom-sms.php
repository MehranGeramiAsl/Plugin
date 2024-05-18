<?php
/*
Plugin Name: custom-sms
Plugin URI: http://yourwebsite.com
Description: A plugin to display WooCommerce order status.
Version: 1.0
Author: Your Name
Author URI: http://yourwebsite.com
License: GPL2
*/

// Function to get order status


define('custom_sms_menu_icon',plugin_dir_url( __FILE__ ). 'assets/image/');


add_action('admin_menu', 'custom_order_status_generator_menu');

function custom_order_status_generator_menu() {
    add_menu_page(
        'SMS سفارشی', // Page title
        'SMS سفارشی', // Menu title
        'manage_options', // Capability required
        'custom_order_status_generator', // Menu slug
        'custom_order_status_generator_page', // Callback function
        custom_sms_menu_icon . 'Theme-icon.png' // Icon
    );
}

// Display the admin page content
function custom_order_status_generator_page() {
    ?>
    <div class="wrap">
        <h2>وضعیت سفارشات</h2>
        <form method="post">
            <label for="customer_name">نام مشتری :</label><br>
            <input type="text" id="customer_name" name="customer_name"><br>
            <label for="order_number">شماره مشتری :</label><br>
            <input type="text" id="order_number" name="order_number"><br>
            <label for="order_status">وضعیت مشتری :</label><br>
            <input type="text" id="order_status" name="order_status"><br><br>
            <input type="submit" name="generate_status" class="button button-primary"  value="بررسی وضعیت">
        </form>
        <?php
        if (isset($_POST['generate_status'])) {
            $customer_name = sanitize_text_field($_POST['customer_name']);
            $order_number = sanitize_text_field($_POST['order_number']);
            $order_status = sanitize_text_field($_POST['order_status']);

            // Log the generated status
            error_log("نام مشتری: $customer_name, شماره سفارش: $order_number, وضعیت سفارش: $order_status");

            echo "<p> نام مشتری : $customer_name, شماره سفارش: $order_number, وضعیت مشتری  : $order_status</p>";
        }
        ?>
    </div>
    <?php
}

add_action('woocommerce_order_status_changed', 'send_sms_notification', 10, 4);

function send_sms_notification($order_id, $old_status, $new_status, $order){
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

// function get_order_status($order_id) {
//     $order = wc_get_order($order_id);
//     if ($order) {
//         return $order->get_status();
//     } else {
//         return 'Order not found';
//     }
// }

// // Shortcode to display order status
// function display_order_status($atts) {
//     // Extract shortcode attributes
//     $atts = shortcode_atts(array(
//         'order_id' => '',
//     ), $atts, 'order_status');

//     // Get order status
//     $order_status = get_order_status($atts['order_id']);

//     // Return order status
//     return $order_status;
// }
// add_shortcode('order_status', 'display_order_status');

// function custom_sms_conten(){
   
//     include(custom_sms_menu_view . 'custom_menu_view.php');
// }

// function custom_sms_menu(){
//     $menu_suffix = add_menu_page( 
//         'پیامک سفارشی',
//         'پیامک سفارشی ',
//         'manage_options', 
//         'custom_sms', 
//         'custom_sms_conten', 
//         custom_sms_menu_icon . 'Theme-icon.png'
//     );
// }

// add_action( 'admin_menu', 'custom_sms_menu' );


// add_action('admin_enqueue_scripts', function() {
 
//     wp_enqueue_style(
//         'sms-style',
//         custom_sms_menu_css . 'sms.css',
//         [],
//         WP_DEBUG ? time() : CUSTOM_LOGIN_VER
        
//     );
// });