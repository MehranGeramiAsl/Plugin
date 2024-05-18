<?PHP
/**
 * Plugin Name: Custom-Style
 * Description: افزودن استایل سفارشی
 */


defined('ABSPATH') || exit;
define('custom_style_menu_admin',plugin_dir_path(__FILE__) . 'admin/');
define('custom_style_menu_view',plugin_dir_path( __FILE__ ). 'view/');
define('custom_style_menu_icon',plugin_dir_url( __FILE__ ). 'assets/image/');

if(is_admin()){

include(custom_style_menu_admin . '/menus.php');

}else{
    add_action('wp_head', function(){
        $savedstyle = get_option( 'kitline-custom-style', '' );
        printf('<style>%s</style>', $savedstyle);
    });
    add_action('wp_footer', function(){
        $savedscript = get_option('kitline-custom-script', '');
        printf('<script>%s</script>', $savedscript);
    });
}

add_action('login_enqueue_scripts', function(){
    
    wp_enqueue_script( 
        'custom-script', 
        CUSTOM_LOGIN_JS_URL . 'custom-style.js', 
        ['underscore'], 
        WP_DEBUG ? time() : CUSTOM_LOGIN_VER, 
         
     );
});