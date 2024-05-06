<?php

namespace WC_Product_Tax_Price;

use WP_Hinza\Utility;

class Ajax
{
    public function __construct()
    {
        /*$list_function = [];
        foreach ($list_function as $method) {
            add_action('wp_ajax_' . $method, [$this, $method]);
            add_action('wp_ajax_nopriv_' . $method, [$this, $method]);
        }*/
    }

}

new Ajax();