<?php

namespace WC_Product_Tax_Price\admin;

use WC_Product_Tax_Price;

class Admin
{

    public function __construct()
    {
        // add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
    }

    public function admin_assets()
    {
        wp_enqueue_style('wc-product-tax-price', WC_Product_Tax_Price::$plugin_url . '/asset/admin/css/style.css', array(), WC_Product_Tax_Price::$plugin_version, 'all');
        wp_enqueue_script('wc-product-tax-price', WC_Product_Tax_Price::$plugin_url . '/asset/admin/js/script.js', array('jquery'), WC_Product_Tax_Price::$plugin_version, false);
        wp_localize_script('wc-product-tax-price', 'wc_product_tax_price_js', array(
            'ajax_url' => admin_url('admin-ajax.php')
        ));
    }
}

new Admin();