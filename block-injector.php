<?php
/**
 * Plugin Name: Block Injector
 * Description: A WordPress plugin that lets you inject content into Post and Page content.
 * Version: 1.0.1
 * Plugin URI: https://pootlepress.com/block-injector/
 * Author: PootlePress
 * Author URI: https://pootlepress.com/
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

$router = new PMAB_Admin( PMAB_Plugin::instance( __FILE__ ), new PMAB_Content() );

add_action( 'plugins_loaded', array( $router, 'init' ) );

if ( ! function_exists( 'block_injector_fs' ) ) {
	// Create a helper function for easy SDK access.
	function block_injector_fs() {
		global $block_injector_fs;

		if ( ! isset( $block_injector_fs ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/wp-sdk/start.php';

			$block_injector_fs = fs_dynamic_init( array(
				'id'                  => '9001',
				'slug'                => 'block-injector',
				'type'                => 'plugin',
				'public_key'          => 'pk_dc610c4658e1657b7bd55e7b9ffff',
				'is_premium'          => true,
				'is_premium_only'     => true,
				'has_addons'          => false,
				'has_paid_plans'      => true,
				'is_org_compliant'    => false,
				'trial'               => array(
					'days'               => 14,
					'is_require_payment' => true,
				),
				'menu'                => array(
					'slug'           => 'block-injector-intro',
					'support'        => false,
					'parent'         => array(
						'slug' => 'edit.php?post_type=block_injector',
					),
				),
				// Set the SDK to work in a sandbox mode (for development & testing).
				// IMPORTANT: MAKE SURE TO REMOVE SECRET KEY BEFORE DEPLOYMENT.
				'secret_key'          => 'sk_1f9RvZfA8Jl&0$:2$grzt}FxhOIxQ',
			) );
		}

		return $block_injector_fs;
	}

	// Init Freemius.
	block_injector_fs();
	// Signal that SDK was initiated.
	do_action( 'block_injector_fs_loaded' );
}
