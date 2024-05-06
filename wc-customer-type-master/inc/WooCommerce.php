<?php

namespace WC_Customer_Type;

class WooCommerce
{

    public static $sessionCustomerType = 'customer_type';

    public static $defaultCustomerType = 1;

    public function __construct()
    {
        // Get Option
        $option = Option::get();

        // Disable Email
        if (isset($option['disable_billing_email']) and $option['disable_billing_email'] == "1") {
            add_filter('woocommerce_checkout_fields', [$this, 'disable_billing_email'], 20, 1);
        }

        // Disable Require Email
        if (isset($option['require_billing_email']) and $option['require_billing_email'] == "2") {
            add_filter('woocommerce_checkout_fields', [$this, 'require_billing_email'], 21, 1);
        }

        // Add ACF User Field
        add_action('acf/init', [$this, 'acf_init']);

        // Show Customer Type Field
        if (isset($option['customer_type_field']) and $option['customer_type_field'] == "1") {
            /**
             * https://www.businessbloomer.com/woocommerce-add-shipping-phone-checkout/
             * https://www.businessbloomer.com/woocommerce-move-reorder-fields-checkout-page/
             * https://www.businessbloomer.com/woocommerce-checkout-customization/
             * https://stackoverflow.com/questions/36705713/how-to-save-woocommerce-checkout-custom-fields-to-user-meta
             * https://rudrastyh.com/woocommerce/checkout-fields.html
             * https://stackoverflow.com/questions/67518033/how-to-add-2-select-fields-combobox-to-woocommerce-checkout-page
             * https://stackoverflow.com/questions/62555891/save-woocommerce-custom-session-variables-as-order-meta-data
             * https://rajaamanullah.com/how-to-use-woocommerce-sessions-and-cookies/
             * https://stackoverflow.com/questions/62555891/save-woocommerce-custom-session-variables-as-order-meta-data
             */
            add_action('woocommerce_init', [$this, 'setup_session']);
            add_action('woocommerce_checkout_create_order', [$this, 'unset_session_checkout_create_order']);
            add_action('wp', [$this, 'set_customer_type_checkout'], 20);
            add_filter('woocommerce_checkout_get_value', [$this, 'woocommerce_checkout_get_value'], 10, 2);
            // add_action('woocommerce_checkout_update_order_review', [$this, 'woocommerce_checkout_update_order_review']);
            add_action('wp_footer', [$this, 'js_on_checkout'], 20);
            add_filter('woocommerce_checkout_fields', [$this, 'woocommerce_checkout_fields'], 22, 1);
            add_action('woocommerce_checkout_update_user_meta', [$this, 'woocommerce_checkout_update_user_meta'], 20, 2);
            add_action('woocommerce_checkout_update_order_meta', [$this, 'woocommerce_checkout_update_order_meta'], 20, 1);
            add_action('woocommerce_admin_order_data_after_billing_address', [$this, 'woocommerce_admin_order_data_after_billing_address'], 30);
        }

        // Add New Item To Order Array API
        add_filter("woocommerce_rest_prepare_shop_order_object", [$this, 'woocommerce_rest_prepare_shop_order_object'], 50, 3);
    }

    /* @Hook */
    public function disable_billing_email($fields)
    {
        if (isset($fields['billing']['billing_email'])) {
            unset($fields['billing']['billing_email']);
        }
        return $fields;
    }

    /* @Hook */
    public function require_billing_email($fields): array
    {
        if (isset($fields['billing']['billing_email'])) {
            $fields['billing']['billing_email']['required'] = false;
        }
        return $fields;
    }

    /* @Hook */
    public function acf_init()
    {
        acf_add_local_field_group(array(
            'key' => 'group_65f3f430a91ec',
            'title' => 'نوع مشتری',
            'fields' => array(
                array(
                    'key' => 'field_65f3f431e7e89',
                    'label' => 'نوع مشتری',
                    'name' => 'customer_type',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        1 => 'حقیقی',
                        2 => 'حقوقی',
                    ),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                array(
                    'key' => 'field_65f3f45ae7e8a',
                    'label' => 'شناسه / کد ملی',
                    'name' => 'national_id',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_65f3f489e7e8b',
                    'label' => 'شماره ثبت',
                    'name' => 'register_number',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_65f3f4bce7e8c',
                    'label' => 'کد اقتصادی',
                    'name' => 'economic_code',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'user_form',
                        'operator' => '==',
                        'value' => 'edit',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }

    /* @Hook */
    public function setup_session()
    {
        if (isset(WC()->session) && !WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }

    /* @Hook */
    public function unset_session_checkout_create_order($order)
    {
        $data = WC()->session->get(self::$sessionCustomerType);
        if (!empty($data)) {
            WC()->session->__unset(self::$sessionCustomerType);
        }
    }

    /* @Hook */
    public function woocommerce_checkout_get_value($value, $input)
    {
        // @see https://wp-kama.ru/plugin/woocommerce/function/WC_Checkout::get_value
        if ($input == "customer_type") {
            return self::get_session_customer_type_checkout();
        }

        return $value;
    }

    /* @Hook */
    public function woocommerce_checkout_fields($fields): array
    {
        // Get Option
        $option = Option::get();

        // Get Customer Type
        $customerType = self::get_session_customer_type_checkout();

        // Add Customer Type Select
        $fields['billing']['customer_type'] = array(
            'label' => 'نوع مشتری',
            'placeholder' => 'نوع مشتری',
            'required' => true,
            'class' => array('form-row-wide'),
            'clear' => true,
            'priority' => 1,
            'default' => $customerType,
            'type' => 'select',
            'options' => array(
                '1' => 'حقیقی',
                '2' => 'حقوقی',
            )
        );

        // Remove Field first_name and last_name in and Require Company Name in Type 2
        if ($customerType == "2") {

            unset($fields['billing']['billing_first_name']);
            unset($fields['billing']['billing_last_name']);
            $fields['billing']['billing_company']['required'] = true;
        }

        // Remove Company Name For Type 1
        if ($customerType == "1" and isset($option['disable_billing_company_type_1']) and $option['disable_billing_company_type_1'] == "1") {
            unset($fields['billing']['billing_company']);
        }

        // Add National ID
        $isRequired = true;
        if (($customerType == "1" and $option['require_national_id_type_1'] == "2") || ($customerType == "2" and $option['require_national_id_type_2'] == "2")) {
            $isRequired = false;
        }
        $fields['billing']['national_id'] = array(
            'type' => 'text',
            'class' => array('form-row-wide'),
            'label' => ($customerType == "1" ? 'کد ملی' : 'شناسه ملی'),
            'placeholder' => ($customerType == "1" ? 'کد ملی' : 'شناسه ملی'),
            'required' => $isRequired,
            'priority' => 30,
        );

        // Add Register Code
        if ($customerType == "2") {

            $fields['billing']['register_number'] = array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => 'شماره ثبت',
                'placeholder' => 'شماره ثبت',
                'required' => ($option['require_register_number'] == "1"),
                'priority' => 35,
            );
        }

        // Add Economic Code
        if ($customerType == "2" || ($customerType == "1" and $option['show_economic_code_type_1'] == "1")) {

            $isRequired = true;
            if (($customerType == "1" and $option['require_economic_code_type_1'] == "2") || ($customerType == "2" and $option['require_economic_code_type_2'] == "2")) {
                $isRequired = false;
            }
            $fields['billing']['economic_code'] = array(
                'type' => 'text',
                'class' => array('form-row-wide'),
                'label' => 'کد اقتصادی',
                'placeholder' => 'کد اقتصادی',
                'required' => $isRequired,
                'priority' => 35,
            );
        }

        // Return
        return $fields;
    }

    /* @Hook */
    public function woocommerce_checkout_update_user_meta($customer_id, $posted)
    {
        if (isset($posted['customer_type']) and !empty($posted['customer_type'])) {
            update_user_meta($customer_id, 'customer_type', trim($posted['customer_type']));
        }

        if (isset($posted['national_id']) and !empty($posted['national_id'])) {
            update_user_meta($customer_id, 'national_id', trim($posted['national_id']));
        }

        if (isset($posted['register_number']) and !empty($posted['register_number'])) {
            update_user_meta($customer_id, 'register_number', trim($posted['register_number']));
        }

        if (isset($posted['economic_code']) and !empty($posted['economic_code'])) {
            update_user_meta($customer_id, 'economic_code', trim($posted['economic_code']));
        }
    }

    /* @Hook */
    public function woocommerce_checkout_update_order_meta($order_id)
    {
        if (!empty($_POST['customer_type'])) {
            update_post_meta($order_id, 'customer_type', sanitize_text_field($_POST['customer_type']));
        }

        if (!empty($_POST['national_id'])) {
            update_post_meta($order_id, 'national_id', sanitize_text_field($_POST['national_id']));
        }

        if (!empty($_POST['register_number'])) {
            update_post_meta($order_id, 'register_number', sanitize_text_field($_POST['register_number']));
        }

        if (!empty($_POST['economic_code'])) {
            update_post_meta($order_id, 'economic_code', sanitize_text_field($_POST['economic_code']));
        }
    }

    /* @Hook */
    public function woocommerce_admin_order_data_after_billing_address($order)
    {
        $order_id = $order->get_id();
        $customer_type = get_post_meta($order_id, 'customer_type', true);
        $national_id = get_post_meta($order_id, 'national_id', true);
        $register_number = get_post_meta($order_id, 'register_number', true);
        $economic_code = get_post_meta($order_id, 'economic_code', true);

        if (!empty($customer_type)) {
            echo '<p><strong>نوع مشتری:</strong> ' . ($customer_type == "1" ? 'حقیقی' : 'حقوقی') . '</p>';
        }

        if (!empty($national_id)) {
            echo '<p><strong>شناسه ملی:</strong> ' . $national_id . '</p>';
        }

        if (!empty($register_number)) {
            echo '<p><strong>شماره ثبت:</strong> ' . $register_number . '</p>';
        }

        if (!empty($economic_code)) {
            echo '<p><strong>کد اقتصادی:</strong> ' . $economic_code . '</p>';
        }
    }

    /* @Hook */
    public function set_customer_type_checkout()
    {
        if (is_checkout() and !is_wc_endpoint_url() and isset($_GET['set-customer-type']) and in_array($_GET['set-customer-type'], [1, 2])) {
            WC()->session->set(self::$sessionCustomerType, (int)$_GET['set-customer-type']);
            wp_redirect(wc_get_checkout_url());
            exit;
        }
    }

    /* @Hook */
    public function woocommerce_checkout_update_order_review($posted_data)
    {
        // Parsing posted data on checkout
        $post = [];
        $vars = explode('&', $posted_data);
        foreach ($vars as $k => $value) {
            $v = explode('=', urldecode($value));
            $post[$v[0]] = $v[1];
        }

        if (isset($post['customer_type']) and in_array($post['customer_type'], [1, 2])) {
            WC()->session->set(self::$sessionCustomerType, (int)$post['customer_type']);
        }
    }

    /* @Hook */
    public function js_on_checkout()
    {
        if (is_checkout() and !is_wc_endpoint_url()) {
            ?>
            <script>
                jQuery(document).ready(function ($) {
                    $('#customer_type').change(function () {
                        // jQuery(document.body).trigger('update_checkout');
                        window.$checkoutUrl = '<?php echo wc_get_checkout_url(); ?>';
                        let checkoutUrl = new URL(window.$checkoutUrl);
                        checkoutUrl.searchParams.append('set-customer-type', $(this).val());
                        window.location = checkoutUrl.toString();
                    });
                });
            </script>
            <?php
        }
    }

    /* @Method */
    public static function get_session_customer_type_checkout()
    {
        $data = WC()->session->get(self::$sessionCustomerType);
        if (!empty($data) and is_numeric($data) and in_array($data, [1, 2])) {
            return $data;
        }

        if (is_user_logged_in()) {
            $data = self::get_customer_type(get_current_user_id());
            if (!empty($data) and is_numeric($data) and in_array($data, [1, 2])) {
                return $data;
            }
        }

        return self::$defaultCustomerType;
    }

    /* @Method */
    public static function get_customer_type($user_id = null): string
    {
        $user_id = (is_null($user_id) ? get_current_user_id() : $user_id);
        return get_user_meta($user_id, 'customer_type', true);
    }

    /* @Method */
    public static function get_customer_type_name($user_id = null): string
    {
        $user_id = (is_null($user_id) ? get_current_user_id() : $user_id);
        $value = get_user_meta($user_id, 'customer_type', true);
        if ($value == "1") {
            return 'حقیقی';
        } elseif ($value == "2") {
            return 'حقوقی';
        }

        return '';
    }

    /* @Method */
    public static function get_customer_national_id($user_id = null): string
    {
        $user_id = (is_null($user_id) ? get_current_user_id() : $user_id);
        return get_user_meta($user_id, 'national_id', true);
    }

    /* @Method */
    public static function get_customer_register_number($user_id = null): string
    {
        $user_id = (is_null($user_id) ? get_current_user_id() : $user_id);
        return get_user_meta($user_id, 'register_number', true);
    }

    /* @Method */
    public static function get_customer_economic_code($user_id = null): string
    {
        $user_id = (is_null($user_id) ? get_current_user_id() : $user_id);
        return get_user_meta($user_id, 'economic_code', true);
    }

    /* @Hook */
    public function woocommerce_rest_prepare_shop_order_object($response, $order, $request)
    {
        if (empty($response->data)) {
            return $response;
        }

        $response->data['customer_type'] = get_post_meta($order->get_id(), 'customer_type', true);
        $response->data['economic_code'] = get_post_meta($order->get_id(), 'economic_code', true);
        $response->data['register_number'] = get_post_meta($order->get_id(), 'register_number', true);
        $response->data['national_id'] = get_post_meta($order->get_id(), 'national_id', true);
        return $response;
    }

}

new WooCommerce();