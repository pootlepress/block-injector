<?php
/**
 * Plugin Name: Block Injector
 * Description: A WordPress plugin that lets you inject content into Post and Page content.
 * Version: 1.0.0
 * Author: PootlePress
 * Author URI: https://github.com/pootlepress/put-blocks-anywhere
 * Text Domain: block-injector
 *
 * @package Block Injector
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
require_once 'inc/helper.php';
require_once 'inc/class-content.php';
require_once 'inc/class-plugin.php';
require_once 'inc/class-admin.php';

$router = new PMAB_Admin( new PMAB_Plugin( __FILE__ ), new PMAB_Content() );

add_action( 'plugins_loaded', array( $router, 'init' ) );
