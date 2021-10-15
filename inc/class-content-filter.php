<?php
/**
 * Class PMAB_Content_Filter
 *
 * @package BlockInjector
 */
class PMAB_Content_Filter {

	/**
	 *
	 * @param $post_id
	 * @param $term_ids
	 * @param $taxonomy
	 * @param array $exclude_posts
	 *
	 * @return bool
	 */
	protected static function current_single_post_has_matching_terms( $term_ids, $taxonomy, $exclude_posts = [] ) {

		$post_id = get_post()->ID;

		// Page is singular post page and post isn't excluded
		if ( ! is_singular() || in_array( $post_id, $exclude_posts, false ) ) {
			return false;
		}

		$applied_terms = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
		$term_ids      = PMAB_Content_Filter::maybe_explode( $term_ids );

		if ( is_wp_error( $applied_terms ) ) {
			return false;
		}

		foreach ( $applied_terms as $tag ) {
			if ( in_array( "$tag", $term_ids ) ) {
				return true;
			}
		}

		return false;
	}


	// Content Filter Hook.

	/**
	 * @param mixed $content
	 * @param mixed $p
	 * @param mixed $tag
	 * @param mixed $num_of_blocks
	 *
	 * @return mixed
	 */
	static public function filter_hook( $content, $p, $tag, $num_of_blocks ) {
		extract( self::extract_meta_for_filter_hook( $p ) );

		$checks = array(
			'woo_all_category_pages' => array( 'is_product_category', null ),
			'woo_category_page'      => array( 'is_product_category', $specific_woocategory ),
			'woo_checkout'           => array( 'is_checkout', null ),
			'woo_account'            => array( 'is_account_page', null ),
			'woo_basket'             => array( 'is_cart', null ),
		);

		// Check if we're inside the main loop in a single Post.
		if ( array_key_exists( $inject_content_type, $checks ) ) {
			$variables = $checks[ $inject_content_type ];
			if ( self::check( $variables[0], $variables[1] ) ) {
				return pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p );
			}
		}

		switch ( $inject_content_type ) {
			case 'woo_all_pages':
				if ( class_exists( 'wooCommerce' ) && ( is_woocommerce() || is_front_page() || is_checkout() || is_account_page() || is_cart() ) ) {
					if ( $tag == 'top' && ( PMAB_Content_Filter::check( 'is_shop' ) || PMAB_Content_Filter::check( 'is_product' ) ) ) {
						break;
					} else {
						return pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p );
					}
				}
				break;
			case 'woo_shop':
				if ( self::check( 'is_shop' ) ) {

					if ( $tag === 'bottom' ) {

						pmab_custom_hook_content( 'woocommerce_after_shop_loop', $content, $tag, 1, $p );
						break;
					} else {

						return pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p );
						break;
					}
				}
				break;
			case 'tags':
				if ( self::current_single_post_has_matching_terms( $tags, 'post_tag', $thisposts_exclude ) ) {
					return pmab_update_content( $content, $tag, $num_of_blocks, $p );
				}
			case 'category':
				if ( self::current_single_post_has_matching_terms( $category, 'category', $thisposts_exclude ) ) {
					return pmab_update_content( $content, $tag, $num_of_blocks, $p );
				}
				break;
			case 'post':
				return pmab_posts_filter_content( $specific_post, $thisposts_exclude, $content, $tag, $num_of_blocks, $p, 'is_single' );
				break;
			case 'page':
				return pmab_posts_filter_content( $specific_post, $thisposts_exclude, $content, $tag, $num_of_blocks, $p, 'is_page' );
				break;
			case 'all_post':
				if ( is_single() && ! is_singular( 'product' ) ) {
					return pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p );
				}
				break;
			case 'all_page':
				if ( is_page() ) {
					return pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p );
				}
				break;
			case 'post_page':
				if ( is_page() || is_single() ) {
					return pmab_filter_exclude_content( $thisposts_exclude, $content, $tag, $num_of_blocks, $p );
				}
				break;
		}

		return $content;
	}

	/**
	 * Fetches required meta data for a block injector post
	 *
	 * @param WP_Post $p
	 *
	 * @return array
	 */
	static public function extract_meta_for_filter_hook( $p ) {
		$tags = get_post_meta( $p->ID, '_pmab_meta_tags', true );

		$specific_post = get_post_meta( $p->ID, '_pmab_meta_specific_post', true );
		$specific_post_exclude = get_post_meta( $p->ID, '_pmab_meta_specific_post_exclude', true );
		$specific_woocategory = get_post_meta( $p->ID, '_pmab_meta_specific_woocategory', true );

		return array(
			'inject_content_type'   => get_post_meta( $p->ID, '_pmab_meta_type', true ),
			'specific_post'         => PMAB_Content_Filter::maybe_explode( $specific_post ),
			'specific_woocategory'  => PMAB_Content_Filter::maybe_explode( $specific_woocategory ),
			'specific_post_exclude' => $specific_post_exclude,
			'thisposts_exclude'     => PMAB_Content_Filter::maybe_explode( $specific_post_exclude ),
			'tags'                  => $tags,
			'category'              => get_post_meta( $p->ID, '_pmab_meta_category', true ),
			'woo_category'          => get_post_meta( $p->ID, '_pmab_meta_woo_category', true ),
			'priority'              => get_post_meta( $p->ID, '_pmab_meta_priority', true ),
			'tag_type'              => get_post_meta( $p->ID, '_pmab_meta_tag_n_fix', true ),
		);
	}

	public static function maybe_explode( $var ) {
		if ( is_string( $var ) ) {
			return explode( ',', str_replace( ' ', '', $var ) );
		}

		return $var;
	}

	public static function check( $function, $add_param = null ) {
		if ( function_exists( $function ) ) {
			if ( $add_param ) {
				return $function( $add_param );
			}

			return $function();
		}
	}

}