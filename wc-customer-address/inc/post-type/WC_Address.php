<?php

namespace WC_Customer_Address;

use WP_Hinza\ParsiDate;
use WP_Hinza\Utility;

class WC_Address
{
    public static $slug = 'wc-address';

    public static $name = 'آدرس مشتریان';

    public function __construct()
    {

        // Setup Post Type
        add_action('init', [$this, 'setup']);

        // Before Delete Item
        add_action('before_delete_post', [$this, 'before_delete_post'], 99, 2);

        // Save Post
        add_action('save_post', [$this, 'save_post'], 20, 3);

        // Remove All Address when Deleted User
        add_action('deleted_user', [$this, 'deleted_user'], 15, 3);

        // Add Action row for users
        add_action('user_row_actions', [$this, 'user_row_actions'], 30, 2);

        // Add Admin Menu
        add_action('admin_menu', array($this, 'admin_menu'), 30, 1);

        // Post Type Column
        add_filter('manage_' . self::$slug . '_posts_columns', [$this, 'column']);
        add_action('manage_' . self::$slug . '_posts_custom_column', [$this, 'column_value'], 10, 2);

        // Change Text Of Published
        add_filter('post_updated_messages', array($this, 'post_updated_messages'));

        // Acf Field Init
        // @see https://github.com/Hube2/acf-dynamic-ajax-select-example/blob/master/dynamic-select-example/my-acf-extension.php
        add_action('acf/init', [$this, 'acf_init']);
        add_filter('acf/load_field/key=field_65f2ef41c54fa', [$this, 'acf_load_country_field_choices']);
        add_filter('acf/load_field/key=field_65f2ef59c54fb', [$this, 'acf_load_state_field_choices']);
        add_filter('acf/load_field/key=field_65f2ef6ac54fc', [$this, 'acf_load_city_field_choices']);
        add_action('acf/input/admin_enqueue_scripts', array($this, 'acf_enqueue_script'));
        add_action('wp_ajax_wc_customer_address_load_state_choices', array($this, 'wc_customer_address_load_state_choices'));

        // Edit Bulk Action
        add_filter('bulk_actions-edit-' . self::$slug, array($this, 'bulk_actions'));

        // Remove Post Row Action
        add_filter('post_row_actions', array($this, 'action_row'), 10, 2);

        // Add Search Box Field
        add_filter('admin_post_type_search_box_fields', array($this, 'search_box_field'));
        add_action('admin_footer', array($this, 'admin_assets_js'));
        add_action('pre_get_posts', array($this, 'search_box_pre_get_wp_query'));
    }

    /* @Hook */
    public function setup()
    {
        // Add Post Type
        $labels = array(
            'name' => self::$name,
            'singular_name' => self::$name,
            'menu_name' => self::$name,
            'name_admin_bar' => self::$name,
            'add_new' => __('افزودن', ''),
            'add_new_item' => __('افزودن', ''),
            'new_item' => __('ایجاد', ''),
            'edit_item' => __('ویرایش', ''),
            'view_item' => __('نمایش', ''),
            'all_items' => __('تمامی', ''),
            'search_items' => __('جستجو', ''),
            'parent_item_colon' => __('والد:', ''),
            'not_found' => __('هیچ آیتمی وجود ندارد.', ''),
            'not_found_in_trash' => __('هیچ آیتمی در سطل زباله یافت نشد.', '')
        );
        $args = array(
            'labels' => $labels,
            'description' => '',
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'query_var' => false,
            'has_archive' => false,
            'hierarchical' => false,
            'capability_type' => 'post',
            'menu_icon' => 'dashicons-email',
            'map_meta_cap' => true,
            'supports' => [
                'custom-fields',
            ]
        );
        register_post_type(self::$slug, $args);
    }

    /* @Hook */
    public function admin_menu()
    {
        add_submenu_page('woocommerce', self::$name, self::$name, 'manage_options', 'edit.php?post_type=' . self::$slug, null);
    }

    /* @method */
    public static function get_country($country_code = false)
    {

        // get all countries
        $countries = WC()->countries->get_countries();
        if ($country_code === false) {
            return $countries;
        }

        // get all states
        $states = WC()->countries->get_states();
        if (!array_key_exists($country_code, $countries)) {
            return false;
        }

        $country = [
            'code' => $country_code,
            'name' => $countries[$country_code],
        ];

        $local_states = array();
        if (isset($states[$country_code])) {
            foreach ($states[$country_code] as $state_code => $state_name) {
                $local_states[$state_code] = $state_name;
            }
        }
        $country['states'] = $local_states;

        // return
        return $country;
    }

    /* @method */
    public static function get_default_country_code()
    {
        return WC()->countries->get_base_country();
    }

    /* @method */
    public static function prepare($item): array
    {
        return [
            'id' => $item->ID,
            'date' => $item->post_date,
            'customer_id' => (int)get_post_meta($item->ID, 'customer_id', true),
            'country' => get_post_meta($item->ID, 'country', true),
            'country_name' => get_post_meta($item->ID, 'country_name', true),
            'state' => get_post_meta($item->ID, 'state', true),
            'state_name' => get_post_meta($item->ID, 'state_name', true),
            'city' => get_post_meta($item->ID, 'city', true),
            'address' => get_post_meta($item->ID, 'address', true),
            'phone' => get_post_meta($item->ID, 'phone', true),
            'zipcode' => get_post_meta($item->ID, 'zipcode', true),
            'default' => (get_post_meta($item->ID, 'default', true) == "1"), # 1 => true 2 => false
            'defaultRaw' => get_post_meta($item->ID, 'default', true)
        ];
    }

    /* @method */
    public static function get($post_id)
    {
        $post = get_post($post_id);

        if (is_null($post)) {
            return null;
        }

        if ($post->post_type != self::$slug) {
            return null;
        }

        // date_format: 'l j F Y ساعت h:i'
        return self::prepare($post);
    }

    /* @method */
    public static function list($arg = []): array
    {
        $default = [
            'post_type' => self::$slug,
            'posts_per_page' => -1,
            'order' => 'DESC',
            'post_status' => 'publish',
            'return' => 'all',
            /**
             * 'meta_query' => [
             * [
             * 'key' => 'customer_id',
             * 'value' => '1',
             * 'compare' => '='
             * ]
             * ]
             */
        ];
        $args = wp_parse_args($arg, $default);

        $posts = Utility::wp_query($args);
        if (empty($posts)) {
            return [];
        }

        $list = [];
        foreach ($posts as $item) {
            $list[] = self::prepare($item);
        }

        return $list;
    }

    /* @method */
    public static function add($arg = [])
    {
        // Default Params
        $default = [
            'customer_id' => get_current_user_id(),
            'country' => '',
            'state' => '',
            'city' => '',
            'address' => '',
            'phone' => '',
            'zipcode' => '',
            'default' => 2
        ];
        $args = wp_parse_args($arg, $default);

        // Uppercase
        $args['country'] = strtoupper($args['country']);

        // Insert
        $post_id = wp_insert_post([
            'post_type' => self::$slug,
            'post_status' => 'publish',
            'post_date' => current_time('mysql'),
            'post_author' => 1,
            'post_title' => '',
            'meta_input' => $args
        ], false, false);
        if (is_wp_error($post_id)) {

            return [
                'status' => false,
                'message' => $post_id->get_error_message()
            ];
        }

        // do action
        do_action('add_wc_customer_address', $post_id);

        // Return Post ID
        return [
            'status' => true,
            'post_id' => $post_id
        ];
    }

    /* @method */
    public static function update($ID, $arg = [])
    {
        // Default Params
        $default = [
            'customer_id' => get_current_user_id(),
            'country' => '',
            'state' => '',
            'city' => '',
            'address' => '',
            'phone' => '',
            'zipcode' => '',
            'default' => 2
        ];
        $args = wp_parse_args($arg, $default);

        // Uppercase
        $args['country'] = strtoupper($args['country']);

        // Insert
        $post_id = wp_update_post([
            'ID' => $ID,
            'meta_input' => $args
        ], false, false);
        if (is_wp_error($post_id)) {

            return [
                'status' => false,
                'message' => $post_id->get_error_message()
            ];
        }

        // do action
        do_action('edited_wc_customer_address', $ID);

        // Return Post ID
        return [
            'status' => true,
            'post_id' => $ID
        ];
    }

    /* @method */
    public static function delete($post_id)
    {
        wp_delete_post($post_id, true);
        return true;
    }

    /* @Hook */
    public function before_delete_post($post_id, $post)
    {
        if (self::$slug !== $post->post_type) {
            return;
        }

    }

    /* @Hook */
    public function deleted_user($user_id, $reassign, $user)
    {
        $list = self::list([
            'meta_query' => [
                [
                    'key' => 'customer_id',
                    'value' => $user_id,
                    'compare' => '='
                ]
            ]
        ]);
        if (!empty($list)) {
            foreach ($list as $item) {
                self::delete($item['id']);
            }
        }
    }

    /* @Hook */
    public function user_row_actions($actions, $user)
    {
        $actions['address'] = '<a href="' . add_query_arg([
                'post_type' => self::$slug,
                'search-type' => 'customer_id',
                's' => $user->id
            ], admin_url('edit.php')) . '">آدرس ها</a>';
        return $actions;
    }

    /* @Hook */
    public function save_post($post_id, $post, $update)
    {
        // Check Post type or post status
        if ($post->post_type != self::$slug || $post->post_status != 'publish') {
            return;
        }

        // Get Items
        $item = self::get($post_id);

        // Save Country and State Name
        $countryList = self::get_country();
        $stateList = self::get_country($item['country']);
        update_post_meta($post_id, 'country_name', $countryList[$item['country']]);
        update_post_meta($post_id, 'state_name', $stateList['states'][$item['state']]);

        // Get Another Address for this Customer
        $list = self::list([
            'post__not_in' => array((int)$post_id),
            'meta_query' => [
                [
                    'key' => 'customer_id',
                    'value' => get_post_meta($post_id, 'customer_id', true),
                    'compare' => '='
                ]
            ]
        ]);


        // Check is first set force default true
        if ($item['default'] === false and empty($list)) {
            update_post_meta($item['id'], 'default', '1');
        }

        // set Another to false default
        if ($item['default'] === true and !empty($list)) {
            foreach ($list as $item) {
                update_post_meta($item['id'], 'default', '2');
            }
        }
    }

    /* @Hook */
    public function column($columns)
    {
        if (isset($columns['title'])) {
            unset($columns['title']);
        }

        if (isset($columns['date'])) {
            unset($columns['date']);
        }

        $columns['customer'] = 'نام مشتری';
        $columns['created_at'] = 'تاریخ ایجاد';
        $columns['country'] = 'کشور';
        $columns['state'] = 'استان';
        $columns['city'] = 'شهر';
        $columns['address'] = 'آدرس';
        $columns['phone'] = 'تلفن';
        $columns['zipcode'] = 'کدپستی';
        $columns['isDefault'] = 'پیش فرض';
        return $columns;
    }

    /* @Hook */
    public function column_value($column, $post_id)
    {
        $item = self::get($post_id);

        switch ($column) {
            case "created_at":

                echo ParsiDate::jdate("Y-m-d", $item['date'], 'eng');
                echo '<br />';
                echo ParsiDate::jdate("H:i", $item['date'], 'eng');
                break;
            case "customer":

                $user = get_userdata($item['customer_id']);
                echo '<a href="' . get_edit_user_link($item['customer_id']) . '" target="_blank">' . $user->display_name . '</a>';
                break;

            case "country":

                echo $item['country_name'];
                echo '<br />';
                echo $item['country'];
                break;

            case "state":

                echo $item['state_name'];
                echo '<br />';
                echo $item['state'];
                break;

            case "city":
            case "address":
            case "phone":
            case "zipcode":

                echo($item[$column] == "" ? '_' : $item[$column]);
                break;
            case "isDefault":

                echo '<span class="dashicons dashicons-' . ($item['default'] === true ? 'yes' : 'no') . '"></span>';
                break;
            default:
                return '';
                break;
        }
    }

    /* @Hook */
    public function post_updated_messages($messages)
    {
        $messages[self::$slug][6] = 'اطلاعات ذخیره شد';
        $messages[self::$slug][7] = 'اطلاعات ذخیره شد';
        $messages[self::$slug][8] = 'اطلاعات ذخیره شد';
        $messages[self::$slug][1] = 'اطلاعات ذخیره شد';
        $messages[self::$slug][2] = 'اطلاعات ذخیره شد';
        $messages[self::$slug][4] = 'اطلاعات ذخیره شد';

        //return the new messaging
        return $messages;
    }

    /* @Hook */
    public function bulk_actions($actions)
    {
        unset($actions['edit']);
        //unset($actions['trash']);
        return $actions;
    }

    /* @Hook */
    public function action_row($actions, $post)
    {
        if ($post->post_type == self::$slug) {
            unset($actions['inline hide-if-no-js']);
        }

        return $actions;
    }

    /* @Hook */
    public function acf_init()
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group(array(
            'key' => 'group_65f2edfc02583',
            'title' => 'آدرس مشتری',
            'fields' => array(
                array(
                    'key' => 'field_65f2edfdc54f9',
                    'label' => 'نام مشتری',
                    'name' => 'customer_id',
                    'aria-label' => '',
                    'type' => 'user',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'role' => '',
                    'return_format' => 'id',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'bidirectional' => 0,
                    'bidirectional_target' => array(),
                ),
                array(
                    'key' => 'field_65f2ef41c54fa',
                    'label' => 'کشور',
                    'name' => 'country',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'ui' => 1,
                    'ajax' => 1,
                    'placeholder' => '',
                ),
                array(
                    'key' => 'field_65f2ef59c54fb',
                    'label' => 'استان',
                    'name' => 'state',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'ui' => 1,
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                array(
                    'key' => 'field_65f2ef6ac54fc',
                    'label' => 'شهر',
                    'name' => 'city',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'ui' => 1,
                ),
                array(
                    'key' => 'field_65f2ef78c54fd',
                    'label' => 'آدرس',
                    'name' => 'address',
                    'aria-label' => '',
                    'type' => 'textarea',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'rows' => '',
                    'placeholder' => '',
                    'new_lines' => '',
                ),
                array(
                    'key' => 'field_65f2ef8bc54fe',
                    'label' => 'تلفن',
                    'name' => 'phone',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_65f2ef98c54ff',
                    'label' => 'کد پستی',
                    'name' => 'zipcode',
                    'aria-label' => '',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'maxlength' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => 'field_65f2efa1c5500',
                    'label' => 'پیش فرض',
                    'name' => 'default',
                    'aria-label' => '',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        1 => 'آری',
                        2 => 'خیر',
                    ),
                    'default_value' => false,
                    'return_format' => 'value',
                    'multiple' => 0,
                    'allow_null' => 0,
                    'ui' => 0,
                    'ajax' => 0,
                    'placeholder' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => self::$slug,
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
            'show_in_rest' => 0,
        ));
    }

    /* @Hook */
    public function acf_load_country_field_choices($field)
    {
        // $field['choices'] = self::get_country();
        $field['choices'] = ['IR' => 'ایران'];
        return $field;
    }

    /* @Hook */
    public function acf_load_state_field_choices($field)
    {
        global $typenow, $post;

        if (Utility::is_edit_page('edit') and $typenow == self::$slug) {
            $country = get_post_meta($post->ID, 'country', true);
            if (!empty($country)) {

                $countries = self::get_country($country);
                $field['choices'] = $countries['states'];
            }
        }

        return $field;
    }

    /* @Hook */
    public function acf_load_city_field_choices($field)
    {
        global $typenow, $post;

        if (Utility::is_edit_page('edit') and $typenow == self::$slug) {
            $state = get_post_meta($post->ID, 'state', true);
            if (!empty($state)) {

                $cities = City::list($state);
                $field['choices'] = [];
                foreach ($cities as $item) {
                    $field['choices'][$item[0]] = $item[0];
                }
            }
        }

        return $field;
    }

    /* @Hook */
    public function acf_enqueue_script()
    {

        global $post;
        if (!$post || !isset($post->ID) || get_post_type($post->ID) != self::$slug) {
            return;
        }

        $handle = 'my-acf-extension';
        $src = \WC_Customer_Address::$plugin_url . '/asset/admin/js/acf-dynamic-select-on-select.js';
        $depends = array('acf-input');
        wp_enqueue_script($handle, $src, $depends);
    }

    /* @Hook */
    public function wc_customer_address_load_state_choices()
    {
        if (!wp_verify_nonce($_POST['nonce'], 'acf_nonce')) {
            die();
        }

        $country = ($_POST['country'] ?? '');

        // Get List
        $states = WC_Address::get_country(strtoupper(trim($country)));
        $choices = array();
        foreach ($states['states'] as $value => $label) {
            $choices[] = array('value' => $value, 'label' => $label);
        }

        // Success
        wp_send_json($choices, 200);
    }

    /* @Hook */
    public function admin_assets_js()
    {
        global $pagenow;
        if ($pagenow == "edit.php" and isset($_GET['post_type']) and $_GET['post_type'] == self::$slug) {
            include \WC_Customer_Address::$plugin_path . '/inc/post-type/view/search-box.php';
        }
    }

    /* @Hook */
    public function search_box_field($list)
    {
        global $post_type;

        if ($post_type == self::$slug) {
            $list = array(
                'customer' => 'نام و نام خانوادگی',
                'customer_id' => 'شناسه کاربر',
                'phone' => 'تلفن',
                'state_name' => 'استان',
                'address' => 'آدرس',
                'country' => [
                    'title' => 'کشور',
                    'type' => 'select',
                    'choices' => self::get_country()
                ]
            );
        }

        return $list;
    }

    /* @Hook */
    public function search_box_pre_get_wp_query($query)
    {
        global $post_type;
        if (is_admin() and $query->is_main_query() and isset($_REQUEST['s']) and isset($_REQUEST['search-type']) and !empty($_REQUEST['s']) and $post_type == self::$slug) {

            // Get Search Type
            $search_type = sanitize_text_field($_REQUEST['search-type']);
            $search = sanitize_text_field($_REQUEST['s']);

            // Disable Default Search
            $query->set('s', null);

            // Set New Condition
            switch ($search_type) {
                case "customer":
                    $customer_ids = [0];
                    $getUserIds = Utility::getUsers([
                        'search' => '*' . esc_attr($search) . '*',
                        'search_columns' => array('display_name'),
                    ]);
                    if (!empty($getUserIds)) {
                        $customer_ids = $getUserIds;
                    }

                    $query->set('meta_query', array(
                        array(
                            'key' => 'customer_id',
                            'compare' => 'IN',
                            'value' => $customer_ids
                        )
                    ));
                    break;

                case "customer_id":
                case "country":
                    $query->set('meta_query', array(
                        array(
                            'key' => $search_type,
                            'compare' => '=',
                            'value' => $search
                        )
                    ));
                    break;

                case "phone":
                case "state_name":
                case "address":

                    $query->set('meta_query', array(
                        array(
                            'key' => $search_type,
                            'compare' => 'LIKE',
                            'value' => $search
                        )
                    ));
                    break;
                default:
            }
        }
    }

}

new WC_Address();