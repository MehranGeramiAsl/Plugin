<?php

namespace WC_Customer_Address\admin;

use WC_Customer_Address;

class Admin
{

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
    }

    public function admin_assets()
    {
        wp_enqueue_style('wc-customer-address', WC_Customer_Address::$plugin_url . '/asset/admin/css/style.css', array(), WC_Customer_Address::$plugin_version, 'all');
        wp_enqueue_script('wc-customer-address', WC_Customer_Address::$plugin_url . '/asset/admin/js/script.js', array('jquery'), WC_Customer_Address::$plugin_version, false);
        wp_localize_script('wc-customer-address', 'wc_customer_address_js', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}

new Admin();