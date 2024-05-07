<?PHP

/**
 * plugin name: custom-login
 * description: سفارشی سازی صفحه لاگین
 */

 defined('ABSPATH') || exit;
 define('CUSTOM_LOGIN_VER', '1.0.0');
 define('CUSTOM_LOGIN_ASSETS_URL', plugin_dir_url( __FILE__ ) .'assets/');
 define('CUSTOM_LOGIN_CSS_URL', CUSTOM_LOGIN_ASSETS_URL. 'css/');
 define('CUSTOM_LOGIN_JS_URL', CUSTOM_LOGIN_ASSETS_URL. 'js/');
 define('CUSTOM_LOGIN_IMAGES_URL', CUSTOM_LOGIN_ASSETS_URL. 'images/');
 
 add_action('login_enqueue_scripts', function() {
 
     wp_enqueue_style(
         'login-style',
         CUSTOM_LOGIN_CSS_URL . 'login.css',
         [],
         WP_DEBUG ? time() : CUSTOM_LOGIN_VER
         // 'screen and (max-width: 600px)'
     );

    $background_image = CUSTOM_LOGIN_IMAGES_URL . 'Hinza-background.jpg';
    $bell_image       = CUSTOM_LOGIN_IMAGES_URL . 'notificator.svg';
    $colors           = ['red', 'green', 'blue', '#e4e4e3', 'white','#9e84a0','#a1b65e','#627ba4','#6497b1'];
    $background_color = $colors[rand(0,count($colors)-1)];


     wp_add_inline_style( 
        'login-style',
        "
        #login{
            background-color: $background_color;
        }

        body{
            background: url('$background_image');
        }
        .login h1 a{
            background-image: url('$bell_image');
        }

        "
     );
     wp_enqueue_script( 
        'login-script', 
        CUSTOM_LOGIN_JS_URL . 'login.js', 
        [], 
        WP_DEBUG ? time() : CUSTOM_LOGIN_VER, 
         
     );
 });


// add_action('login_head',function(){
//     echo '<link rel="stylesheet" type="text/css" href="'.CUSTOM_LOGIN_CSS_URL.  'login.css"/>';
// });

// add_action('login_head', function(){

//     echo '<style>';
//     echo file_get_contents(CUSTOM_LOGIN_CSS_URL . 'login.css');
//     echo '</style>';

// });
