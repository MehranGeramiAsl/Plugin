<?php

namespace WC_Customer_Address;

class WooCommerce
{

    public static $MenuSlug = 'customer_address';

    public function __construct()
    {
        $option = Option::get();
        if (isset($option['is_active_wc_page']) and $option['is_active_wc_page'] == "1") {

            add_filter('woocommerce_account_menu_items', [$this, 'woocommerce_account_menu_items']);
            add_action('init', [$this, 'add_page_endpoint']);
            add_action('woocommerce_account_' . self::$MenuSlug . '_endpoint', [$this, 'wc_page']);
        }

        // Setup CheckOut Action
        if (!empty($option['checkout_hook_page'])) {
            add_action($option['checkout_hook_page'], [$this, 'checkout']);
        }

        // Setup Receiver Field
        if ($option['show_receiver_field'] == "1") {
            add_action($option['receiver_hook_page'], [$this, 'billing_fields']);
            add_action('woocommerce_checkout_process', [$this, 'woocommerce_checkout_process']);
            add_action('woocommerce_checkout_update_order_meta', [$this, 'woocommerce_checkout_update_order_meta']);
            add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'woocommerce_admin_order_data_after_billing_address']);
        }

        // Add Cities Select Box in CheckOut
        if (apply_filters('wp_hinza_customer_address_hidden_billing_country', true) === true) {
            add_filter('woocommerce_checkout_fields', [$this, 'woocommerce_checkout_fields']);
            add_action('wp_head', [$this, 'wp_head']);
        }
        add_action('wp_enqueue_scripts', [$this, 'wp_enqueue_scripts']);
    }

    /* @Hook */
    public function woocommerce_account_menu_items($menu_links): array
    {
        $option = Option::get();

        // Setup List
        $lists = [];
        foreach ($menu_links as $menu_key => $menu_name) {
            if ($menu_key == "edit-account") {
                $lists[self::$MenuSlug] = $option['menu_name'];
            }

            $lists[$menu_key] = $menu_name;
        }

        // Check Delete Default Address
        if (isset($option['delete_address_menu']) and $option['delete_address_menu'] == "1" and isset($lists['edit-address'])) {
            unset($lists['edit-address']);
        }

        // Return
        return $lists;
    }

    /* @Hook */
    public function add_page_endpoint()
    {
        add_rewrite_endpoint(self::$MenuSlug, EP_PAGES);
    }

    /* @Method */
    public static function get_template_path($file): string
    {
        // https://wordpress.stackexchange.com/a/248822
        $default = \WC_Customer_Address::$plugin_path . '/templates/wc-customer-address/' . $file;
        $templatePath = get_theme_file_path('wc-customer-address/' . $file);
        if (file_exists($templatePath)) {
            return $templatePath;
        }

        return $default;
    }

    /* @Method */
    public static function get_page_url($args = [])
    {
        // @see https://stackoverflow.com/questions/56257832/woocommerce-get-endpoint-url-not-returning-correctly
        return add_query_arg($args, wc_get_endpoint_url(self::$MenuSlug, '', get_permalink(get_option('woocommerce_myaccount_page_id'))));
    }

    /* @Hook */
    public function wc_page()
    {
        // Message
        $message = null;

        // Get Option List
        $option = Option::get();

        // Get User ID
        $user_id = get_current_user_id();

        // Get Default
        $default_country = 'IR';
        if ($option['limit_iran_country'] == "2") {
            $default_country = WC_Address::get_default_country_code();
        }
        $country_choices = WC_Address::get_country();
        $default_choices = [1 => 'آری', 2 => 'خیر'];
        $default_state = apply_filters('wp_hinza_customer_address_default_state', 'THR');
        $default_city = apply_filters('wp_hinza_customer_address_default_city', 'تهران');

        // Setup Edit Form Data
        if (isset($_GET['ID'])) {

            $item = WC_Address::get((int)$_GET['ID']);
            $default_country = $item['country'];
            $default_state = $item['state'];
            $default_city = $item['city'];
            if (is_null($item) || $item['customer_id'] != $user_id) {
                wp_die('دسترسی به صفحه فوق ندارید');
            }
        }

        // Get List States
        $states = WC_Address::get_country($default_country);

        // Get List Of cities
        $cities = City::list($default_state);

        // Saved Address
        if (isset($_POST['ID']) and isset($_POST['wc_customer_address_nonce']) and wp_verify_nonce($_REQUEST['wc_customer_address_nonce'], 'wc_customer_address_nonce')) {

            $argument = [
                'customer_id' => $user_id,
                'country' => ($option['limit_iran_country'] == "2" ? strtoupper($_POST['country']) : 'IR'),
                'state' => trim($_POST['state']),
                'city' => trim($_POST['city']),
                'address' => trim($_POST['address']),
                'phone' => trim($_POST['phone']),
                'zipcode' => trim($_POST['zipcode']),
                'default' => trim($_POST['default']),
            ];

            if ($_POST['ID'] == "0") {

                $post = WC_Address::add($argument);
                if (!empty($_POST['redirect'])) {
                    ?>
                    <script>window.location.href = "<?php echo $_POST['redirect']; ?>";</script>
                    <?php
                }
            } else {
                $post = WC_Address::update((int)$_POST['ID'], $argument);
            }

            $message = 'آدرس با موفقیت ذخیره شد';
        }

        // Get Customer Address
        $getCustomerAddress = WC_Address::list([
            'meta_query' => [
                [
                    'key' => 'customer_id',
                    'value' => $user_id,
                    'compare' => '='
                ]
            ]
        ]);

        // Allowed To Add Address
        $allowedAddAddress = true;
        if (!empty($option['max_number_address']) and (int)$option['max_number_address'] > 0) {
            if (count($getCustomerAddress) >= (int)$option['max_number_address']) {
                $allowedAddAddress = false;
            }
        }

        // Show Page
        include self::get_template_path('panel.php');
    }

    /* @Hook */
    public function checkout()
    {
        $user_id = get_current_user_id();
        if ($user_id < 1) {
            return;
        }

        // Get Customer Address
        $getCustomerAddress = WC_Address::list([
            'meta_query' => [
                [
                    'key' => 'customer_id',
                    'value' => $user_id,
                    'compare' => '='
                ]
            ]
        ]);

        // Show Page
        include self::get_template_path('checkout.php');
    }

    /* @Hook */
    public function billing_fields($checkout)
    {
        $option = Option::get();
        $isRequireField = ($option['require_receiver_field'] == "1");
        include self::get_template_path('fields.php');
    }

    /* @Hook */
    public function woocommerce_checkout_process()
    {
        $option = Option::get();
        $isRequireField = ($option['require_receiver_field'] == "1");
        if ($isRequireField === false) {
            return;
        }

        if (empty($_POST['receiver-full-name'])) {
            wc_add_notice('نام فرد تحویل گیرنده را وارد نمایید', 'error');
        }

        if (empty($_POST['receiver-phone'])) {
            wc_add_notice('شماره همراه فرد تحویل گیرنده را وارد نمایید', 'error');
        }
    }

    /* @Hook */
    public function woocommerce_checkout_update_order_meta($order_id)
    {
        if (!empty($_POST['receiver-full-name'])) {
            update_post_meta($order_id, 'receiver-full-name', esc_attr($_POST['receiver-full-name']));
        }

        if (!empty($_POST['receiver-phone'])) {
            update_post_meta($order_id, 'receiver-phone', esc_attr($_POST['receiver-phone']));
        }
    }

    /* @Hook */
    public function woocommerce_admin_order_data_after_billing_address($order)
    {
        $order_id = $order->get_id();
        $receiver_FullName = get_post_meta($order_id, 'receiver-full-name', true);
        $receiver_Phone = get_post_meta($order_id, 'receiver-phone', true);
        echo '<p><strong>فرد تحویل گیرنده:</strong> ' . (empty($receiver_FullName) ? '-' : $receiver_FullName) . '</p>';
        echo '<p><strong>شماره تحویل گیرنده:</strong> ' . (empty($receiver_Phone) ? '-' : $receiver_Phone) . '</p>';
    }

    /* @Hook */
    public function woocommerce_checkout_fields($fields)
    {
        $fields['billing']['billing_country']['class'] = 'form-row-hidden';
        return $fields;
    }

    /* @Hook */
    public function wp_head()
    {
        if (!function_exists('is_checkout')) {
            return;
        }

        if (!is_checkout()) {
            return;
        }
        ?>
        <style>
            .form-row-hidden {
                display: none !important;
            }
        </style>
        <?php
    }

    /* @Hook */
    public function wp_enqueue_scripts()
    {
        if (!function_exists('is_checkout')) {
            return;
        }

        if (!is_checkout()) {
            return;
        }

        wp_enqueue_script(
            'wc-city-dropdown',
            \WC_Customer_Address::$plugin_url . '/asset/public/checkout-cities.js',
            ['jquery'],
            \WC_Customer_Address::$plugin_version,
            true
        );
    }

}

new WooCommerce();