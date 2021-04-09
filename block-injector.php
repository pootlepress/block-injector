<?php
/**
 * Plugin Name: Block Injector
 * Description: A WordPress plugin that lets you inject content into Post and Page content.
 * Version: 1.0.0
 * Author: PootlePress
 * Author URI: https://github.com/pootlepress/put-blocks-anywhere
 * Text Domain: block-injector
 *
 * @package PutBlocksAnywhere
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// foreach ( glob( plugin_dir_path( __FILE__ ) . 'php/*.php', GLOB_BRACE ) as $file ) {
// 	require_once $file;
// }
require_once 'php/PMAB_Plugin.php';
require_once 'php/PMAB_Router.php';
require_once 'php/helper.php';
$router = new PMAB_Router( new PMAB_Plugin( __FILE__ ) );

add_action( 'plugins_loaded', array( $router, 'init' ) );


// function myguten_register_post_meta() {
// register_post_meta( 'post', '_myguten_protected_key', array(
// 'show_in_rest' => true,
// 'single' => true,
// 'type' => 'string',
// 'auth_callback' => function() {
// return current_user_can( 'edit_posts' );
// }
// ) );
// }


