<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class WC_Product_Tax_Price
{
    /**
     * Minimum PHP version required
     *
     * @var string
     */
    private $min_php = '7.4.0';

    /**
     * Use plugin's translated strings
     *
     * @var string
     * @default true
     */
    public static $use_i18n = true;

    /**
     * URL to this plugin's directory.
     *
     * @type string
     * @status Core
     */
    public static $plugin_url;

    /**
     * Path to this plugin's directory.
     *
     * @type string
     * @status Core
     */
    public static $plugin_path;

    /**
     * Path to this plugin's directory.
     *
     * @type string
     * @status Core
     */
    public static $plugin_version;

    /**
     * Plugin instance.
     *
     * @see get_instance()
     * @status Core
     */
    protected static $_instance = null;

    /**
     * Plugin Slug
     *
     * @var string
     */
    public static $plugin_slug = 'wc-product-tax-price';

    /**
     * Plugin Main File
     *
     * @var string
     */
    public static $plugin_main_file = '';

    /**
     * Plugin Data
     *
     * @var array
     */
    public static $plugin_data = [];

    /**
     * Access this plugin’s working instance
     *
     * @wp-hook plugins_loaded
     * @return  object of this class
     * @since   2012.09.13
     */
    public static function instance()
    {
        null === self::$_instance and self::$_instance = new self;
        return self::$_instance;
    }

    /**
     * constructor.
     */
    public function __construct()
    {

        /**
         * Check Require Php Version
         */
        if (version_compare(PHP_VERSION, $this->min_php, '<=')) {
            add_action('admin_notices', [$this, 'php_version_notice']);
            return;
        }

        /*
         * Define Variable
         */
        $this->define_constants();

        /**
         * Load Plugin
         */
        add_action('plugins_loaded', [$this, 'plugins_loaded']);
    }

    public function plugins_loaded()
    {
        /**
         * Check Require WooCommerce is Installed
         */
        if (!class_exists('WooCommerce')) {
            add_action('admin_notices', [$this, 'woocommerce_notice']);
            return;
        }

        /**
         * Check required items is installed
         */
        if (!class_exists('acf_pro')) {
            add_action('admin_notices', [$this, 'acf_pro_notice']);
            return;
        }

        /**
         * Setup Custom Filters
         */
        add_filter('wp_hinza_settings_api_menu_prefix_logo_src', function ($src) {
            return rtrim(self::$plugin_url, "/");
        });

        /*
         * include files
         */
        $this->includes();
    }

    /**
     * Define Constant
     */
    public function define_constants()
    {
        if (!function_exists('get_plugin_data')) {
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }

        self::$plugin_url = plugins_url('', __FILE__);
        self::$plugin_path = plugin_dir_path(__FILE__);
        self::$plugin_main_file = self::$plugin_path . 'wc-product-tax-price.php';
        self::$plugin_data = get_plugin_data(self::$plugin_main_file);
        self::$plugin_version = self::$plugin_data['Version'];
    }

    /**
     * include Plugin Require File
     */
    public function includes()
    {
        /**
         * Hinza WordPress API
         */
        require_once dirname(__FILE__) . '/inc/hinza/libs/SettingApi.php';
        require_once dirname(__FILE__) . '/inc/hinza/Utility.php';
        require_once dirname(__FILE__) . '/inc/hinza/Settings.php';
        require_once dirname(__FILE__) . '/inc/hinza/ParsiDate.php';
        require_once dirname(__FILE__) . '/inc/hinza/WP_Mail.php';

        /**
         * Plugin Files
         */
        require_once dirname(__FILE__) . '/inc/Helper.php';
        require_once dirname(__FILE__) . '/inc/Option.php';
        require_once dirname(__FILE__) . '/inc/Ajax.php';
        require_once dirname(__FILE__) . '/inc/admin/admin.php';
        require_once dirname(__FILE__) . '/inc/WooCommerce.php';

        /*
        * init WordPress hook
        */
        $this->init_hooks();

        /*
         * Plugin Loaded Action
         */
        do_action('wc_product_tax_price_loaded');
    }

    /**
     * Used for regular plugin work.
     *
     * @wp-hook init Hook
     * @return  void
     */
    public function init_hooks()
    {
        /**
         * Load i18n
         */
        if (self::$use_i18n === true) {
            load_plugin_textdomain('wc-product-tax-price', false, wp_normalize_path(self::$plugin_path . '/languages'));
        }
    }

    /**
     * On register_activation_hook
     * @return void
     */
    public static function register_activation_hook()
    {
        //
    }

    /**
     * On register_deactivation_hook
     * @return void
     */
    public static function register_deactivation_hook()
    {
        //
    }

    /**
     * Show notice about PHP version
     *
     * @return void
     */
    public function php_version_notice()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        $error = __('Your installed PHP Version is: ', 'wc-product-tax-price') . PHP_VERSION . '. ';
        $error .= __('The <strong>WP Product Tax Price</strong> plugin requires PHP version <strong>', 'wc-product-tax-price') . $this->min_php . __('</strong> or greater.', 'wc-product-tax-price');
        ?>
        <div class="error">
            <p><?php printf($error); ?></p>
        </div>
        <?php
    }

    /**
     * Show Notice about WooCommerce Required
     *
     * @return void
     */
    public function woocommerce_notice()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="error">
            <p>افزونه برای فعال سازی نیاز به افزونه
                <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">
                    فروشگاه ساز ووکامرس (WooCommerce)
                </a>
                دارد ، لطفا نسبت به نصب آن
                اقدام کنید</p>
        </div>
        <?php
    }

    /**
     * Show Notice about ACF Pro Required
     *
     * @return void
     */
    public function acf_pro_notice()
    {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="error">
            <p>افزونه برای فعال سازی نیاز به افزونه
                <a href="#">
                    فیلد های زمینه پیشرفته (Advanced Custom Fields)
                </a>
                دارد ، لطفا نسبت به نصب آن
                اقدام کنید</p>
        </div>
        <?php
    }

}

/**
 * Main instance of WP_Plugin.
 *
 * @since  1.1.0
 */
function wc_product_tax_price_api()
{
    return WC_Product_Tax_Price::instance();
}

// Global for backwards compatibility.
$GLOBALS['wc-product-tax-price'] = wc_product_tax_price_api();
