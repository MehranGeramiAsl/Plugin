<?php

namespace WC_Customer_Address;

class Ajax
{
    public function __construct()
    {
        $list_function = [
            'wc_customer_address_get_states_list',
            'wc_customer_address_get_city_list',
        ];
        foreach ($list_function as $method) {
            add_action('wp_ajax_' . $method, [$this, $method]);
            add_action('wp_ajax_nopriv_' . $method, [$this, $method]);
        }
    }

    public function wc_customer_address_get_states_list()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {

            // Check Require Data
            if (empty($_REQUEST['country'])) {
                exit;
            }

            // Get List
            $states = WC_Address::get_country(strtoupper(trim($_REQUEST['country'])));
            if (!$states) {
                $states = [];
            }

            $choices = array();
            foreach ($states['states'] as $value => $label) {
                $choices[] = array('value' => $value, 'label' => $label);
            }

            // Success
            wp_send_json($choices, 200);
        }
        exit;
    }

    public function wc_customer_address_get_city_list()
    {
        if (defined('DOING_AJAX') && DOING_AJAX) {

            // Check Require Data
            if (empty($_REQUEST['state'])) {
                exit;
            }

            // Get List
            $cities = City::list(strtoupper(trim($_REQUEST['state'])));
            if (empty($cities)) {
                $cities = [];
            }

            $choices = array();
            foreach ($cities as $item) {
                $choices[] = array('value' => $item[0], 'label' => $item[0]);
            }

            // Success
            wp_send_json($choices, 200);
        }
        exit;
    }
}

new Ajax();