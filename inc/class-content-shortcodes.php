<?php
/**
 * Shortcodes for dynamic injections
 *
 * @package BlockInjector
 */

/**
 * Shortcode ib_title
 * @return string
 */
function pmab_shortcode_title() {
	return get_the_title();
}
add_shortcode( 'bi_title', 'pmab_shortcode_title' );

/**
 * Shortcode ib_price
 * @return string
 */
function pmab_shortcode_price() {
	/** @var WC_Product */
	global $product;

	if ( $product ) {
		return wc_price( $product->get_price() );
	}
	return '';
}
add_shortcode( 'bi_price', 'pmab_shortcode_price' );

/**
 * Shortcode ib_product_categories
 * @return string
 */
function pmab_shortcode_product_categories( $atts ) {

	// Attributes
	$atts = shortcode_atts(
		array(
			'sep' => ', ',
			'before' => '',
			'after' => '',
		),
		$atts
	);

	$queried_object = get_queried_object();
	if ( $queried_object && $queried_object->taxonomy == "product_cat" ) {
		return $queried_object->name;
	}

	/** @var WC_Product */
	global $product;

	if ( $product ) {
		return wc_get_product_category_list( $product->get_id(), $atts['sep'], $atts['before'], $atts['after'] );
	}
	return '';
}
add_shortcode( 'bi_product_categories', 'pmab_shortcode_product_categories' );

/**
 * Shortcode ib_stock
 * @return string
 */
function pmab_shortcode_stock() {
	/** @var WC_Product */
	global $product;

	if ( $product ) {
		return $product->get_stock_quantity();
	}
	return '';
}
add_shortcode( 'bi_stock', 'pmab_shortcode_stock' );
