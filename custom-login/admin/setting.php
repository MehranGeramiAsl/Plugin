<?php

add_action('admin_menu', 'custom_login_setting_menu');
function custom_login_setting_menu(){

    $hook_suffix = add_menu_page( 
        'ورود سفارشی', 
        'ورود سفارشی', 
        'manage_options', 
        'custom_login_setting', 
        function(){ 
            $custom_login_options = get_option('custom_login',[]);
            include( CUSTOM_LOGIN_ADMIN_VIEW_PATH . 'setting.php');} ,
        'dashicons-welcome-widgets-menus',
    );

    add_action('load-' . $hook_suffix , function(){

        add_action('admin_enqueue_scripts', 'custom_login_load_setting_script');

        if(isset($_POST['column_color'])){

            $settings                 = get_option('custom_login' , []);
            $settings['column_color'] = sanitize_hex_color($_POST['column_color']);
            update_option('custom_login' , $settings);
            add_action('admin_notices', 'custom_login_success_notices');
        }

});

}

function custom_login_success_notices(){

    echo '
    <div class ="notice notice-success is-dismissible"
        <p>
            تنظیمات ذخیره شد        
        </p>
    </div>
    ';

}

function custom_login_load_setting_script(){
    wp_enqueue_style(
        'wp-color-picker',
    );
    wp_enqueue_script( 
        'custom-login-setting', 
        CUSTOM_LOGIN_JS_URL . 'settings.js', 
        ['wp-color-picker'], 
        WP_DEBUG ? time() : CUSTOM_LOGIN_VER, 
    );

}