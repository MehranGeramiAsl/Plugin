<?php

namespace WC_Customer_Address;

class Option
{

    public static $name = 'wc-customer-address';

    public function __construct()
    {
        add_filter('wp_hinza_settings_api_section', [$this, 'wp_hinza_settings_api_section']);
        add_filter('wp_hinza_settings_api_fields', [$this, 'wp_hinza_settings_api_fields']);
    }

    public function wp_hinza_settings_api_section($section)
    {
        $section[] = [
            'id' => self::$name,
            'title' => __('آدرس مشتریان', ''),
            'desc' => ''
        ];
        return $section;
    }

    public function wp_hinza_settings_api_fields($fields)
    {
        $fields[self::$name] = array(
            array(
                'name' => 'is_active_wc_page',
                'label' => __('فعال سازی منو ووکامرس', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'مشخص کنید منوی ووکامرس فعال باشد یا خیر؟'
            ),
            array(
                'name' => 'menu_name',
                'label' => __('عنوان منو', ''),
                'type' => 'text',
                'default' => 'آدرس های من',
                'desc' => 'عنوان منو ووکامرس را مشخص کنید؟',
                'class' => ''
            ),
            array(
                'name' => 'max_number_address',
                'label' => __('حداکثر تعداد آدرس', ''),
                'type' => 'text',
                'default' => '5',
                'desc' => 'مشخص کنید هر مشتری می توانید چند آدرس تعریف شده داشته باشد؟',
                'class' => ''
            ),
            array(
                'name' => 'checkout_hook_page',
                'label' => __('هوک نمایش در صفحه پرداخت', ''),
                'type' => 'text',
                'default' => 'woocommerce_before_checkout_billing_form',
                'desc' => 'مشخص کنید لیست آدرس ها در کدام هوک نمایش صفحه پرداخت نمایش داده شود؟',
                'class' => 'hinza-input-ltr'
            ),
            array(
                'name' => 'delete_address_menu',
                'label' => __('حذف منو پیش فرض ووکامرس', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '2',
                'desc' => 'مشخص کنید که منوی پیش فرض ووکامرس آدرس ها مخفی شود یا خیر؟'
            ),
            array(
                'name' => 'limit_iran_country',
                'label' => __('محدودیت کشور ایران', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    #'2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا قابلیت انتخاب کشور در فرم آدرس وجود داشته باشد؟'
            ),
            array(
                'name' => 'show_receiver_field',
                'label' => __('نمایش فیلد دریافت کننده', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد نام و شماره همراه دریافت کننده را نمایش داده شود؟'
            ),
            array(
                'name' => 'require_receiver_field',
                'label' => __('ضروری بودن فیلد دریافت کننده', ''),
                'type' => 'select',
                'options' => array(
                    '1' => 'آری',
                    '2' => 'خیر'
                ),
                'default' => '1',
                'desc' => 'آیا فیلد های مربوط به دریافت کننده حتما میبایست پر شود؟'
            ),
            array(
                'name' => 'receiver_hook_page',
                'label' => __('هوک نمایش فیلد دریافت کننده', ''),
                'type' => 'text',
                'default' => 'woocommerce_before_order_notes',
                'desc' => 'مشخص کنید فیلد دریافت کننده در کدام هوک نمایش صفحه پرداخت نمایش داده شود؟',
                'class' => 'hinza-input-ltr'
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