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

namespace pootlepress\PutMeAnywhereBlock;

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

$router = new Router( new Plugin( __FILE__ ) );

add_action( 'plugins_loaded', array( $router, 'init' ) );

$posts = get_posts(
	array(
		'post_type'      => 'ct_content_block',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		// 'fields'         => 'ids',

	)
);
// loop over each post
foreach ( $posts as $p ) {
	$id = $p->ID;
	// get the meta you need form each post
	$num_of_blocks        = get_post_meta( $id, '_pmab_meta_number_of_blocks', true );
	$tag_type             = get_post_meta( $id, '_pmab_meta_tag_n_fix', true );
	$inject_content_type  = get_post_meta( $id, '_pmab_meta_type', true );
	$tag_type             = explode( '_', $tag_type );
			$tag          = $tag_type[0];
			$after_before = $tag_type[1];

	add_filter(
		'the_content',
		function ( $content ) use ( $inject_content_type, $p, $tag, $num_of_blocks, $after_before ) {

			// Check if we're inside the main loop in a single Post.
			if ( $inject_content_type == 'post' && is_single() ) {
				return update_content( $content, $tag, $num_of_blocks, $p, $after_before );
			}
			if ( $inject_content_type == 'page' && is_page() ) {
				return update_content( $content, $tag, $num_of_blocks, $p, $after_before );
			}

			return $content;
		},
		// array( $this, 'test' ),
		100
	);
	// do whatever you want with it
}

function update_content( $content, $tag, $num_of_blocks, $p, $after_before ) {
	// global $content, $tag, $num_of_blocks, $p,$after_before;
	$content_array = explode( "</$tag>", $content );

			$offset = $after_before === 'before' ? ( $num_of_blocks > 0 ) ? count( $content_array ) - 1 - $num_of_blocks : 0 : $num_of_blocks;

			array_splice( $content_array, $offset, 0, array( $p->post_content ) );

			$update_content = implode( "</$tag>", $content_array );

			return $update_content;
}
