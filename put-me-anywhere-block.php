<?php
/**
 * Plugin Name: Put Me Anywhere Block
 * Description: Put Me Anywhere Block for WordPress.
 * Version: 1.0.0
 * Author: pootlepress
 * Author URI: https://github.com/pootlepress/put-me-anywhere-block
 * Text Domain: put-me-anywhere-block
 *
 * @package PutMeAnywhereBlock
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( glob( plugin_dir_path( __FILE__ ) . 'php/*.php', GLOB_BRACE ) as $file ) {
	require_once $file;
}

$router = new PMAB_Router( new PMAB_Plugin( __FILE__ ) );

add_action( 'plugins_loaded', array( $router, 'init' ) );





