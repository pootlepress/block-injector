<?php


class PMAB_Admin_Save_Post {
	protected $location_taxonomy_maps = [
		'post_page'              => 'any,post,page,product,product_cat,shop',
		'all_post'               => 'post',
		'post'                   => 'post,by-id',
		'category'               => 'post,by-category',
		'tags'                   => 'post,by-tags',
		'all_page'               => 'page',
		'page'                   => 'page,by-id',
		'woo_all_pages'          => 'shop,page,product,product_cat,product_tag',
		'woo_all_products'       => 'product',
		'woo_pro_category'       => 'product,by-category',
		'woo_tags'               => 'product,by-tag',
		'woo_product'            => 'product,by-id',
		'woo_all_category_pages' => 'product_cat',
		'woo_category_page'      => 'product_cat',
		'woo_shop'               => 'shop',
		'woo_account'            => 'page',
		'woo_basket'             => 'page',
		'woo_checkout'           => 'page',
	];

	protected $post_metas = [
		'_pmab_meta_number_of_blocks',
		'_pmab_meta_specific_post',
		'_pmab_meta_specific_woocategory',
		'_pmab_meta_specific_post_exclude',
		'_pmab_meta_tags',
		'_pmab_meta_category',
		'_pmab_meta_woo_category',
		'_pmab_meta_type',
		'_pmab_meta_type2',
		'_pmab_meta_tag_n_fix',
		'_pmab_meta_hook',
		'_pmab_meta_expiredate',
		'_pmab_meta_startdate',
	];

	/**
	 * Saves taxonomies for the post based on $location type.
	 * @param $post_id
	 * @param $location
	 */
	protected function save_taxonomies( $post_id, $location ) {
		if ( ! empty( $this->location_taxonomy_maps[$location] ) ) {
			wp_set_object_terms( $post_id, explode( ',', $this->location_taxonomy_maps[$location] ), 'block_injector_location' );
		} else {
			wp_set_object_terms( $post_id, false, 'block_injector_location' );
		}
	}

	protected function save_metas( $post_id ) {
		foreach ( $this->post_metas as $post_meta ) {
			update_post_meta( $post_id, $post_meta, sanitize_text_field( $_POST[$post_meta] ) );
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
			$this->save_metas( $post_id );
			$this->save_taxonomies( $post_id, $_POST['_pmab_meta_type'] );
		}
	}

}