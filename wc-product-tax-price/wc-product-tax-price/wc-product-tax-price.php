<?php
/**
 * Plugin Name: قیمت کالا با ارزش افزوده
 * Description: افزودن قیمت کالا با ارزش افزوده برای مشتریان حقوقی یا حقیقی در سیستم ووکامرس
 * Plugin URI:  https://hinzaco.com/
 * Version:     1.0.0
 * Author:      مهرشاد درزی
 * Author URI:  https://hinzaco.com/
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * License:     MIT
 * Text Domain: wc-product-tax-price
 * Domain Path: /languages
 */

// Include Plugin Source
require_once('bootstrap.php');

// Register Activation Hook
register_activation_hook(__FILE__, ['\WC_Product_Tax_Price', 'register_activation_hook']);
register_deactivation_hook(__FILE__, ['\WC_Product_Tax_Price', 'register_deactivation_hook']);