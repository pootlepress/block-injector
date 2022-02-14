<?php

/**
 * Helper Functions.
 *
 * @package BlockInjector
 */

function pmab_process_injection( $content ) {
	$blocks = parse_blocks( $content );
	$output = '';

	foreach ( $blocks as $block ) {
		$output .= render_block( $block );
	}

	return do_shortcode( $output );
}

if ( ! function_exists( 'pmab_update_content' ) ) {
	/**
	 * @param mixed $content The Content Description.
	 * @param mixed $tag HTML tags.
	 * @param mixed $num_of_blocks before, after blocks.
	 * @param mixed $p post content.
	 *
	 * @return string
	 */
	function pmab_update_content( $content, $tag, $num_of_blocks, $p ) {
		$injection = PMAB_Content::output_injection( $p );

		if ( $num_of_blocks == 0 ) {
			return "$injection $content";
		}

		if ( $num_of_blocks == PHP_INT_MAX ) {
			return "$content $injection";
		}

		$re = '/(<!-- \/[^ ]* -->\n\n)/m';

		if ( $tag == 'h2' ) {
			$re = '/<(h\d)>.*\<\/\1>/m';
		} else if ( false === strpos( $content, '<!-- ' ) ) {
			$re = '/<\/(p|h\d)>|\n\s*\n/m';
		}

		$i = 0;
		$d = preg_replace_callback( $re, function ( $matches ) use ( &$i, $num_of_blocks, $injection ) {
			$i += 1;
			if ( $i == $num_of_blocks ) {
				return $matches[0] . $injection . "\n\n";
			}

			return $matches[0];

		}, $content );

		return $d;
	}
}

if ( ! function_exists( 'pmab_expire_checker' ) ) {

	/**
	 * @param String $starting_date Start Date and Time.
	 * @param String $expiry_date Expire End Date and Time.
	 *
	 * @return Boolean
	 */
	function pmab_expire_checker( string $starting_date, string $expiry_date ) {
		$current_date = date( 'Y-m-d\TH:i' ); // Date object using current date and time

		return ( $starting_date <= $current_date ) || ( $expiry_date >= $current_date && $starting_date <= $current_date );
	}
}

if ( ! function_exists( 'pmab_posts_filter_content' ) ) {
	// Make All filter Content
	/**
	 * @param mixed $posts
	 * @param mixed $thisposts_exclude
	 * @param mixed $content
	 * @param mixed $tag
	 * @param mixed $num_of_blocks
	 * @param mixed $p
	 * @param mixed $function_name
	 *
	 * @return mixed
	 */
	function pmab_posts_filter_content( $posts, $thisposts_exclude, $content, $tag, $num_of_blocks, $p, $function_name ) {
		foreach ( $posts as $post ) {
			if ( is_object( $post ) ) {
				$post = $post->ID;
			}
			if ( $function_name( $post ) ) {

				if ( ! in_array( $post, $thisposts_exclude, true ) ) {

					if ( $tag == 'top' && PMAB_Content_Filter::check( 'is_product' ) ) {
						pmab_custom_hook_content( 'woocommerce_before_single_product', $content, $tag, 1, $p );

						return;
					} else {
						return pmab_update_content( $content, $tag, $num_of_blocks, $p );
					}
				}
				if ( $tag == 'top' && PMAB_Content_Filter::check( 'is_product' ) ) {
					pmab_custom_hook_content( 'woocommerce_before_single_product', $content, $tag, 1, $p );

					return;
				} else {
					return pmab_update_content( $content, $tag, $num_of_blocks, $p );
				}
			}
		}

		return $content;
	}
}
if ( ! function_exists( 'pmab_custom_hook_content' ) ) {
	// Make Custom Hook Content
	/**
	 * @param mixed $custom_hook
	 * @param mixed $content
	 * @param mixed $tag
	 * @param mixed $num_of_blocks
	 * @param mixed $p
	 *
	 * @return mixed
	 */
	function pmab_custom_hook_content( $custom_hook, $content, $tag, $num_of_blocks, $p ) {


		add_action(
			$custom_hook,
			static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
				echo pmab_update_content( $content, $tag, $num_of_blocks, $p );;
			},
			0
		);
	}
}
function print_filters_for( $hook = '' ) {

	global $wp_filter;
	if ( empty( $hook ) || ! isset( $wp_filter[ $hook ] ) ) {
		return false;
	} else {
		return true;
	}
}

if ( ! function_exists( 'pmab_filter_exclude_content' ) ) {
	// Exclude Filter Content
	/**
	 * @param mixed $thisposts_exclude
	 * @param mixed $content
	 * @param mixed $tag
	 * @param mixed $num_of_blocks
	 * @param mixed $p
	 *
	 * @return mixed
	 */
	function pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p ) {
		if ( in_array( get_post()->ID, $thisposts_exclude, false ) ) {
			return $content;
		}

		if ( $tag == 'top' && PMAB_Content_Filter::check( 'is_product' ) ) {
			pmab_custom_hook_content( 'woocommerce_before_single_product', $content, $tag, 1, $p );

			return $content;
		}

		return pmab_update_content( $content, $tag, $num_of_blocks, $p );
	}
}
