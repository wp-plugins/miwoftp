<?php
/*
Plugin Name: MiwoFTP
Plugin URI: http://miwisoft.com
Description: MiwoFTP is a smart, fast and lightweight file manager component. It operates from WordPress back-end so you don't have to use any FTP program anymore.
Author: Miwisoft LLC
Version: 1.0.1
Author URI: http://miwisoft.com
Plugin URI: http://miwisoft.com/wordpress-plugins/miwoftp-wordpress-file-manager
*/

defined('ABSPATH') or die('MIWI');

define('MPATH_MIWOFTP_QX', plugin_dir_path(__FILE__).'admin/quixplorer');
define('MURL_MIWOFTP', plugins_url('', __FILE__));

add_action('init', 'check_init_action');
add_action('admin_menu', 'miwoftp_menu');

function miwoftp_menu() {
    add_menu_page('MiwoFTP', 'MiwoFTP', 'manage_options', 'miwoftp', 'miwoftp_echo', MURL_MIWOFTP.'/admin/assets/images/icon-16-miwoftp.png', '33.0099');
}

function miwoftp_echo() {
    echo '<div class="wrap">';
    echo '<h2>MiwoFTP</h2>';

    ob_start();
    require_once(MPATH_MIWOFTP_QX.'/index.php');
    $output = ob_get_contents();
    ob_end_clean();

    $replace_output = array(
        'index.php?action=' => 'admin.php?page=miwoftp&action=',
        'src="_img' => 'src="'.MURL_MIWOFTP.'/admin/quixplorer/_img',
        //'<TABLE WIDTH="95%">' => '<TABLE WIDTH="95%" class="wp-list-table widefat">'
    );

    foreach($replace_output as $key => $value){
        $output = str_replace($key, $value, $output);
    }

    echo $output;

    echo '<div style="margin: 10px; text-align: center;"><a style="text-decoration: none;" href="http://miwisoft.com/wordpress-plugins/miwoftp-wordpress-file-manager" target="_blank">MiwoFTP | Copyright &copy; 2009-2014 Miwisoft LLC</a></div>';
    echo '</div>';
}

function check_init_action(){
    if(empty($_GET['action']) or (isset($_GET['action']) and $_GET['action'] != 'download') ){
        return;
    }

    if(!isset($_GET['option']) or (isset($_GET['option']) and $_GET['option'] != 'com_miwoftp') ){
        return;
    }

    if(!isset($_GET['item'])) {
        return;
    }

    require MPATH_MIWOFTP_QX."/_include/init.php";

    ob_start(); // prevent unwanted output
   	require MPATH_MIWOFTP_QX."/_include/fun_down.php";
   	ob_end_clean(); // get rid of cached unwanted output
   	download_item($GLOBALS["dir"], $GLOBALS["item"]);
   	ob_start(false); // prevent unwanted output
   	exit;
}