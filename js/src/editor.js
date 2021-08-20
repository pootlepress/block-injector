jQuery( document ).ready( function ( $ ) {
	$( '.js-example-basic-single' ).select2();
	$( '#_pmab_meta_type' ).on( 'change', function () {
		if ( this.value === 'post' || this.value === 'page' || this.value === 'woo_product' ) {
			$( '.specificpost' ).show();
		} else {
			$( '.specificpost' ).hide();
			$( '#_pmab_meta_specific_post' ).val( '' );
		}
		if ( this.value === 'category' ) {
			$( '.category-box' ).show();
		} else {
			$( '.category-box' ).hide();
			$( '#_pmab_meta_category' ).val( '' );
		}
		if ( this.value === 'woo_pro_category' ) {
			$( '.woo-category-box' ).show();
		} else {
			$( '.woo-category-box' ).hide();
			$( '#_pmab_meta_woo_category' ).val( '' );
		}
		if ( this.value === 'woo_category_page' ) {
			$( '.specificwoocategory' ).show();
		} else {
			$( '.specificwoocategory' ).hide();
			$( '#_pmab_meta_specific_woocategory' ).val( '' );
		}
		if ( this.value === 'tags' || this.value === 'woo_tags' ) {
			$( '.tags' ).show();
		} else {
			$( '.tags' ).hide();
			$( '#_pmab_meta_tags' ).val( '' );
		}
	} );
	$( '#_pmab_meta_tag_n_fix' ).on( 'change', function () {
		if ( this.value === 'h2_after' || this.value === 'p_after' ) {
			$( '.certain_num' ).show();
			$( '.woo_hook' ).hide();
			$( '#_pmab_meta_hook' ).val( '' );
		} else if ( this.value === 'woo_hook' ) {
			$( '.woo_hook' ).show();
			$( '.certain_num' ).hide();
			$( '#_pmab_meta_number_of_blocks' ).val( '' );
		} else if ( this.value === 'top_before' || this.value === 'bottom_after' ) {
			$( '.certain_num' ).hide();
			$( '#_pmab_meta_number_of_blocks' ).val( '' );
			$( '#_pmab_meta_hook' ).val( '' );
			$( '.woo_hook' ).hide();

		} else {
			$( '.certain_num' ).hide();
			$( '#_pmab_meta_number_of_blocks' ).val( '' );
			$( '.woo_hook' ).hide();

		}

	} );
	$( '#_pmab_meta_type2' ).on( 'change', function () {
		if ( this.value === 'post_exclude' || this.value === 'page_exclude' ) {
			$( '.specificpost_exclude' ).show();
		} else {
			$( '.specificpost_exclude' ).hide();
			$( '#_pmab_meta_specific_post_exclude' ).val( '' );
		}
	} );
} );
