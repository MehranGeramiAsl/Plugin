<?php

defined('ABSPATH') || exit;

function custom_style_conten(){
   
    include(custom_style_menu_view . 'custom_menu_view.php');
}

function custom_help_tab($screen,$tab){
    if($tab['id']== 'style_help_tab2'){
        echo 'tab 2:';
    }else{
        echo 'tab 1:';
    }
    echo 'this is my heps';
}


function custom_style_proccess(){
    
    /**
     * Manage help tabs
     */
    $screen = get_current_screen(  );
    $screen -> add_help_tab([
        'title' => 'راهنمای استایل',
        'id' => 'style_help_tab',
        'content' => 'راهنما کار با افزونه استایل سفارشی',
        'callback' => 'custom_help_tab',
       
    ]);
    $screen -> add_help_tab([
        'title' => '2راهنمای استایل',
        'id' => 'style_help_tab2',
        'content' => '2راهنما کار با افزونه استایل سفارشی',
        'callback' => 'custom_help_tab',
        'priority' => 1
    ]);

    $screen->set_help_sidebar(
        '<p>سلام</p>
        <p><a href="">مشاهده مستندات</a></p>'
    );
    $GLOBALS['custom_style_notices'] = false;
    if(isset($_POST['custom-style'])){
        $style = trim($_POST['custom-style']);
        $script = trim($_POST['custom-script']);

        $savestyle = update_option( 'kitline-custom-style', $style  );
        $savescript = update_option( 'kitline-custom-script', $script );

            $notice = [
                'type' => 'success',
                'massege' => 'با موفقیت ذخیره شد',
            ];
    $GLOBALS['custom_style_notices'] = $notice;
    }
}
function custom_style_menu(){
    $menu_suffix = add_menu_page( 
        'استایل سفارشی',
        'Js/Css سفارشی',
        'manage_options', 
        'custom_style', 
        'custom_style_conten', 
        custom_style_menu_icon . 'Theme-icon.png', 
    );
   
    add_action('load-' . $menu_suffix ,'custom_style_proccess' );
    add_action('load-tools.php','custom_style_proccess' );
    add_action('load-toplevel_page_hinza','custom_style_proccess' );
 }

 add_action( 'admin_menu', 'custom_style_menu' );