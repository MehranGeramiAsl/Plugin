<?php

namespace WC_Product_Tax_Price;

use WP_Hinza\Utility;
use WP_Hinza\WP_MAIL;

class Helper
{

    /* @Method */
    public static function get_user_mobile($user_id)
    {
        // Get Type Of Mobile
        $option = Option::get();

        // Pre Filter
        $pre = apply_filters('wp_hinza_user_mobile', null, $user_id);
        if (!is_null($pre)) {
            return $pre;
        }

        // Mobile
        $mobile = '';

        // Get Billing phone
        if ($option['basis_mobile'] == 'billing_phone') {
            $mobile = get_user_meta($user_id, 'billing_phone', true);
        }

        // Get UserLogin
        if ($option['basis_mobile'] == 'user_login') {
            $user = get_userdata($user_id);
            $mobile = $user->user_login;
        }

        // Get CustomField
        if ($option['basis_mobile'] == 'custom_field') {
            $customFieldKey = $option['mobile_custom_field'];
            $mobile = get_user_meta($user_id, (empty($customFieldKey) ? 'billing_phone' : $customFieldKey), true);
        }

        // Check Empty
        if (empty($mobile)) {
            return '';
        }

        // Sanitize Mobile
        return Utility::sanitizeMobile($mobile);
    }

    /* @Method */
    public static function get_user_email($user_id)
    {

        // Pre Filter
        $pre = apply_filters('wp_hinza_user_email', null, $user_id);
        if (!is_null($pre)) {
            return $pre;
        }

        // Get User Data
        $user = get_userdata($user_id);
        if (!$user) {
            return '';
        }

        // Check Empty
        if (empty($user->user_email)) {
            return '';
        }

        // Sanitize Email
        return $user->user_email;
    }

    /* @Method */
    public static function get_template_path($file): string
    {
        // https://wordpress.stackexchange.com/a/248822
        $default = \WC_Product_Tax_Price::$plugin_path . '/templates/wc-product-tax-price/' . $file;
        $templatePath = get_theme_file_path('wc-product-tax-price/' . $file);
        if (file_exists($templatePath)) {
            return $templatePath;
        }

        return $default;
    }

    /* @Method */
    public static function get_template_url($file): string
    {
        // https://wordpress.stackexchange.com/a/248822
        $default = \WC_Product_Tax_Price::$plugin_url . '/templates/wc-product-tax-price/' . $file;
        $templateUrl = rtrim(get_stylesheet_directory_uri(), "/") . '/wc-product-tax-price/' . $file;
        if (file_exists($templateUrl)) {
            return $templateUrl;
        }

        return $default;
    }

}