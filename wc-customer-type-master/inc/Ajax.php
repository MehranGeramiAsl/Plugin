<?php

namespace WC_Customer_Type;

class Ajax
{
    public function __construct()
    {
        $methods = [];
        foreach ($methods as $method) {
            add_action('wp_ajax_' . $method, [$this, $method]);
            add_action('wp_ajax_nopriv_' . $method, [$this, $method]);
        }
    }

}

new Ajax();