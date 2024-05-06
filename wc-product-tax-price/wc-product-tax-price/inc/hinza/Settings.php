<?php

namespace WP_Hinza;

/**
 * Class Settings
 *
 * @see https://github.com/tareq1988/wordpress-settings-api-class
 *
 * SELECT * FROM `wp_options` WHERE `option_name` LIKE '%wc_product_tax_price%' ORDER BY `option_id` DESC
 */
if (!class_exists("\WP_Hinza\Settings")) {
    class Settings
    {
        /**
         * Plugin Option name
         */
        public $setting;

        /**
         * The single instance of the class.
         */
        protected static $_instance = null;

        /**
         * Main Instance.
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Admin_Setting_Api constructor.
         */
        public function __construct()
        {
            add_action('admin_init', [$this, 'init_option']);
            add_action('admin_head', [$this, 'admin_head']);
            add_action('admin_menu', [$this, 'admin_menu']);
        }

        /**
         * Admin Menu
         */
        public function admin_menu()
        {
            add_menu_page(
                __('هینزا', ''),
                __('هینزا', ''),
                'manage_options',
                'hinza',
                array($this, 'setting_page'),
                apply_filters('wp_hinza_settings_api_menu_prefix_logo_src', '') . '/inc/hinza/img/icon-menu.png',
                88
            );
        }

        /**
         * Admin Head
         */
        public function admin_head()
        {
            global $pagenow;
            if ($pagenow == "admin.php" and isset($_GET['page']) and $_GET['page'] == "hinza") {
                ?>
                <style>
                    .form-table th {
                        width: 310px !important;
                    }

                    tr.level select {
                        min-width: 30rem !important;
                    }

                    .nav-tab, h2, h3 {
                        font-family: tahoma !important;
                        font-weight: normal !important;
                    }

                    input#submit {
                        width: 200px;
                        height: 50px;
                        margin-top: 25px;
                    }

                    tr.hinza-input-ltr input {
                        text-align: left;
                        direction: ltr;
                    }
                </style>
                <?php
            }
        }

        /**
         * Display the plugin settings options page
         */
        public function setting_page()
        {

            echo '<div class="wrap">';
            settings_errors();

            $this->setting->show_navigation();
            $this->setting->show_forms();

            echo '</div>';
        }

        /**
         * Registers settings section and fields
         */
        public function init_option()
        {
            global $pagenow;

            /**
             * array(
             * array(
             * 'id' => 'id,
             * 'title' => __('title', 'wc-product-tax-price'),
             * 'desc' => ''
             * )
             * );
             */
            $sections = apply_filters('wp_hinza_settings_api_section', []);

            /**
             * Option Data
             * if (is_admin() and $pagenow == "admin.php" and isset($_GET['page']) and $_GET['page'] == 'hinza'}
             */
            $option_data = apply_filters('wp_hinza_settings_api_option_data', []);

            // Set All Settings Field
            $fields = apply_filters('wp_hinza_settings_api_fields', []);

            // New Setting API
            $this->setting = new SettingAPI();

            //set sections and fields
            $this->setting->set_sections($sections);
            $this->setting->set_fields($fields);

            //initialize them
            $this->setting->admin_init();
        }
    }

    new Settings();
}

