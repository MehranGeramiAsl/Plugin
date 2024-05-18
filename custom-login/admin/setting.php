<?php

add_action('admin_menu', 'custom_login_setting_menu');
function custom_login_setting_menu(){

    add_menu_page( 
        'ورود سفارشی', 
        'ورود سفارشی', 
        'manage_options', 
        'custom_login_setting', 
        function(){ include( CUSTOM_LOGIN_ADMIN_VIEW_PATH . 'setting.php');} ,
        'dashicons-welcome-widgets-menus',
    );

}