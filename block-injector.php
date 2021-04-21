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


if (! defined('ABSPATH')) {
    exit;
}
require_once 'inc/helper.php';
require_once 'inc/PMAB_Plugin.php';
require_once 'inc/PMAB_Router.php';

$router = new PMAB_Router(new PMAB_Plugin(__FILE__));

add_action('plugins_loaded', array( $router, 'init' ));
