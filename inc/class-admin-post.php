<?php


class PMAB_Admin_Post {

	/**
	 * Update PMAB_Content::known_query_objects after any changes here.
	 * @uses PMAB_Content::known_query_objects
	 * @var string[] Terms to set for location type
	 */
	protected $location_taxonomy_maps = [
		'post_page'                     => 'any,post,page,product,product_cat,shop',
		'all_post'                      => 'post',
		'post'                          => 'post,by-id',
		'category'                      => 'post,by-category',
		'tags'                          => 'post,by-tags',
		'all_page'                      => 'page',
		'page'                          => 'page,by-id',
		'woo_all_pages'                 => 'shop,page,product,product_cat,product_tag',
		'woo_all_products'              => 'product',
		'woo_all_products_in_stock'     => 'product',
		'woo_all_products_out_of_stock' => 'product',
		'woo_all_products_on_backorder' => 'product',
		'woo_all_products_on_sale'      => 'product',
		'woo_pro_category'              => 'product,by-category',
		'woo_pro_tags'                  => 'product,by-tag',
		'woo_product'                   => 'product,by-id',
		'woo_all_category_pages'        => 'product_cat',
		'woo_category_page'             => 'product_cat',
		'woo_shop'                      => 'shop',
		'woo_account'                   => 'page',
		'woo_basket'                    => 'page',
		'woo_checkout'                  => 'page',
	];

	protected $post_metas = [
		'_pmab_meta_number_of_blocks',
		'_pmab_meta_specific_post',
		'_pmab_meta_priority',
		'_pmab_meta_specific_woocategory',
		'_pmab_meta_specific_post_exclude',
		'_pmab_meta_tags',
		'_pmab_meta_category',
		'_pmab_meta_woo_category',
		'_pmab_meta_type',
		'_pmab_meta_tag_n_fix',
		'_pmab_meta_hook',
		'_pmab_meta_expiredate',
		'_pmab_meta_startdate',
		'_pmab_responsive_visibility',
		'_pmab_meta_on_days',
		'_pmab_meta_from_time',
		'_pmab_meta_to_time',
	];

	protected function post_type_args() {
		return array(
			'labels' => array(
				'name'               => __( 'Block Injector', 'pmab' ),
				'singular_name'      => __( 'Block Injector', 'pmab' ),
				'add_new'            => __( 'Add New', 'pmab' ),
				'add_new_item'       => __( 'Add New Block Injector', 'pmab' ),
				'edit_item'          => __( 'Edit Block Injector', 'pmab' ),
				'new_item'           => __( 'New Block Injector', 'pmab' ),
				'all_items'          => __( 'Block Injector', 'pmab' ),
				'view_item'          => __( 'View Block Injector', 'pmab' ),
				'search_items'       => __( 'Search Block Injector', 'pmab' ),
				'not_found'          => __( 'Nothing found', 'pmab' ),
				'not_found_in_trash' => __( 'Nothing found in Trash', 'pmab' ),
				'parent_item_colon'  => '',
			),

			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'publicly_queryable'  => false,
			'can_export'          => true,
			'query_var'           => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'show_in_rest'        => true,
			'exclude_from_search' => true,

			'supports' => array(
				'title',
				'editor',
			),

			'capabilities' => array(
				'edit_post'              => 'edit_pages',
				'read_post'              => 'edit_pages',
				'delete_post'            => 'edit_pages',
				'edit_posts'             => 'edit_pages',
				'edit_others_posts'      => 'edit_pages',
				'publish_posts'          => 'edit_pages',
				'read_private_posts'     => 'edit_pages',
				'read'                   => 'edit_pages',
				'delete_posts'           => 'edit_pages',
				'delete_private_posts'   => 'edit_pages',
				'delete_published_posts' => 'edit_pages',
				'delete_others_posts'    => 'edit_pages',
				'edit_private_posts'     => 'edit_pages',
				'edit_published_posts'   => 'edit_pages',
			),
		);
	}

	/**
	 * Saves taxonomies for the post based on $location type.
	 *
	 * @param $post_id
	 * @param $location
	 */
	protected function save_taxonomies( $post_id, $location ) {
		if ( ! empty( $this->location_taxonomy_maps[ $location ] ) ) {
			wp_set_object_terms( $post_id, explode( ',', $this->location_taxonomy_maps[ $location ] ), 'block_injector_location' );
		} else {
			wp_set_object_terms( $post_id, false, 'block_injector_location' );
		}
	}

	protected function save_metas( $post_id ) {

		$multi_values = [
			'_pmab_meta_category',
			'_pmab_meta_specific_woocategory',
			'_pmab_meta_woo_category',
			'_pmab_meta_specific_post',
			'_pmab_meta_on_days',
		];

		foreach ( $multi_values as $multi_value ) {
			if ( ! empty( $_POST[ $multi_value ] ) && is_array( $_POST[ $multi_value ] ) ) {
				$_POST[ $multi_value ] = implode( ',', $_POST[ $multi_value ] );
			}
		}

		foreach ( $this->post_metas as $post_meta ) {
			update_post_meta( $post_id, $post_meta, sanitize_text_field( $_POST[ $post_meta ] ) );
		}
	}

	/**
	 * Save the meta box container.
	 *
	 * @param mixed $post_id The post object.
	 *
	 * @return void
	 */
	public function save_post( $post_id ) {
		if (
			isset( $_POST['_pmab_meta_number_of_blocks'], $_POST['_pmab_meta_type'], $_POST['pmab_plugin_field'] ) &&
			wp_verify_nonce( $_POST['pmab_plugin_field'], 'pmab_plugin_nonce' )
		) {
//			$this->save_status( $post );
			$this->save_metas( $post_id );
			$this->save_taxonomies( $post_id, $_POST['_pmab_meta_type'] );
		}
	}

	private function save_status( $post ) {
		global $wpdb;

		$old_status = $post->post_status;

		if ( empty( $_POST['_pmab_post_published'] ) ) {
			if ( $old_status === 'publish' ) {
				$post->post_status = 'draft';
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'draft' ), array( 'ID' => $post->ID ) );
			}
		} else if ( $post->post_status !== 'publish' ) {
			$post->post_status = 'publish';
			$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $post->ID ) );
		}
	}
}
