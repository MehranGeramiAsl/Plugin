<?php

namespace WC_Product_Tax_Price;

class Option
{

    public static $name = 'wc-product-tax-price';

    public function __construct()
    {
        add_filter('wp_hinza_settings_api_section', [$this, 'wp_hinza_settings_api_section']);
        add_filter('wp_hinza_settings_api_fields', [$this, 'wp_hinza_settings_api_fields']);
    }

    public function wp_hinza_settings_api_section($section)
    {
        $section[] = [
            'id' => self::$name,
            'title' => __('قیمت کالا با ارزش افزوده', ''),
            'desc' => ''
        ];
        return $section;
    }

    public function wp_hinza_settings_api_fields($fields)
    {

        // Setup Field
        $fields[self::$name] = array(
            array(
                'name' => 'is_active',
                'label' => __('فعال سازی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا قابلیت فعال باشد یا خیر؟'
            ),
            array(
                'name' => 'price_customer_type_1',
                'label' => __('نمایش قیمت برای مشتری حقیقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'قیمت پیش فرض ووکامرس',
                    '2' => 'قیمت با ارزش افزوده'
                ),
                'default' => '1',
                'desc' => 'مشخص کنید کاربران حقیقی چه نوع قیمتی را در سایت مشاهده کنید؟'
            ),
            array(
                'name' => 'calculate_tax_customer_type_1',
                'label' => __('جداسازی ارزش افزوده با قیمت / حقیقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '2',
                'desc' => 'مشخص کنید در صورتی که قیمت با ارزش افزوده برای مشتریان حقیقی در حال نمایش است ، عدد ارزش افزوده جداسازی شود؟'
            ),
            array(
                'name' => 'price_customer_type_2',
                'label' => __('نمایش قیمت برای مشتری حقوقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'قیمت پیش فرض ووکامرس',
                    '2' => 'قیمت با ارزش افزوده'
                ),
                'default' => '2',
                'desc' => 'مشخص کنید کاربران حقوقی چه نوع قیمتی را در سایت مشاهده کنید؟'
            ),
            array(
                'name' => 'calculate_tax_customer_type_2',
                'label' => __('جداسازی ارزش افزوده با قیمت / حقوقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'مشخص کنید در صورتی که قیمت با ارزش افزوده برای مشتریان حقوقی در حال نمایش است ، عدد ارزش افزوده جداسازی شود؟'
            ),
        );

        return $fields;
    }

    public static function get()
    {
        return get_option(self::$name, []);
    }
}

new Option();