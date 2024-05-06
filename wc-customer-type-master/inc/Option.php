<?php

namespace WC_Customer_Type;

class Option
{

    public static $name = 'wc-customer-type';

    public function __construct()
    {
        add_filter('wp_hinza_settings_api_section', [$this, 'wp_hinza_settings_api_section']);
        add_filter('wp_hinza_settings_api_fields', [$this, 'wp_hinza_settings_api_fields']);
    }

    public function wp_hinza_settings_api_section($section)
    {
        $section[] = [
            'id' => self::$name,
            'title' => __('نوع مشتریان', ''),
            'desc' => ''
        ];
        return $section;
    }

    public function wp_hinza_settings_api_fields($fields)
    {
        $fields[self::$name] = array(
            array(
                'name' => 'disable_billing_email',
                'label' => __('حذف فیلد پست الکترونیک', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '2',
                'desc' => 'آیا فیلد پست الکترونیک نمایش داده شود یا خیر؟'
            ),
            array(
                'name' => 'require_billing_email',
                'label' => __('ضروری بودن فیلد پست الکترونیک', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد پست الکترونیک ضروری باشد یا خیر؟'
            ),
            array(
                'name' => 'customer_type_field',
                'label' => __('نمایش فیلد نوع مشتری', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد نوع مشتری در صفحه پرداخت نمایش داده شود یا خیر؟'
            ),
            array(
                'name' => 'disable_billing_company_type_1',
                'label' => __('غیرفعال کردن فیلد نام شرکت شخص حقیقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '2',
                'desc' => 'آیا فیلد نام شرکت برای شخص حقیقی غیر فعال شود؟'
            ),
            array(
                'name' => 'require_national_id_type_1',
                'label' => __('الزامی بودن کد ملی شخص حقیقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد کد ملی برای اشخاص حقیقی الزامی باشد؟'
            ),
            array(
                'name' => 'require_national_id_type_2',
                'label' => __('الزامی بودن شناسه ملی شخص حقوقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد کد ملی برای اشخاص حقوقی الزامی باشد؟'
            ),
            array(
                'name' => 'require_register_number',
                'label' => __('الزامی بودن شماره ثبت', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد شماره ثبت برای اشخاص حقوقی الزامی باشد؟'
            ),
            array(
                'name' => 'show_economic_code_type_1',
                'label' => __('نمایش کد اقتصادی برای شخص حقیقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '2',
                'desc' => 'آیا فیلد کد اقتصادی برای شخص حقیقی هم نمایش داده شود؟'
            ),
            array(
                'name' => 'require_economic_code_type_1',
                'label' => __('الزامی بودن کد اقتصادی / حقیقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '2',
                'desc' => 'آیا فیلد کد اقتصادی برای شخص حقیقی الزامی باشد؟'
            ),
            array(
                'name' => 'require_economic_code_type_2',
                'label' => __('الزامی بودن کد اقتصادی / حقوقی', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد کد اقتصادی برای شخص حقوقی الزامی باشد؟'
            )
        );

        return $fields;
    }

    public static function get()
    {
        return get_option(self::$name, []);
    }
}

new Option();