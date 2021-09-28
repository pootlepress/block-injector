jQuery( document ).ready( function ( $ ) {

	$( '.pmab-multi-select' ).select2();

	$postsPicker = $( 'select#_pmab_meta_specific_post' );

	function setupPostOptions( postType ) {
		// Terminate if posts data is not yet available
		if ( !pmabProps.allPosts ) {
			return;
		}

		var posts = pmabProps.allPosts[postType];
		var preselectedPosts = '' + $postsPicker.data('value');
		preselectedPosts = ( preselectedPosts || '' ).split(',');
		var selectedPosts = [];

		if ( posts ) {
			console.log( posts );
			$postsPicker
			$postsPicker.html( "" );
			$( posts ).each( function ( i ) {
				var pid = posts[i][0];
				if ( preselectedPosts.indexOf( pid ) > -1 ) {
					selectedPosts.push( pid );
				}
				$postsPicker.append(
					"<option value=" + pid + ">" + pid + ': ' + posts[i][1] + "</option>"
				);
			} );

			$postsPicker.val( selectedPosts ).change();
		}

	}

	$injType = $( '#_pmab_meta_type' );

	$injType.on( 'change', function () {
		if ( this.value.indexOf( 'woo' ) === 0 ) {
			$( '.pmab-no-woo' ).hide();
		} else {
			$( '.pmab-no-woo' ).show();
		}

		if ( this.value === 'woo_all_products' || this.value === 'woo_product' ) {
			$( '.pmab-product-options' ).show();
		} else {
			$( '.pmab-product-options' ).hide();
		}

		if ( this.value === 'post' || this.value === 'page' || this.value === 'woo_product' ) {
			$( '.pmab-specific-posts' ).show();
			setupPostOptions( this.value === 'woo_product' ? 'product' : this.value );
		} else {
			$( '.pmab-specific-posts' ).hide();
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
		if ( this.value === 'tags' || this.value === 'woo_pro_tags' ) {
			$( '.tags' ).show();
		} else {
			$( '.tags' ).hide();
			$( '#_pmab_meta_tags' ).val( '' );
		}
	} );

	$injType.change();

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
			$( '.pmab-specific-posts-exclude' ).show();
		} else {
			$( '.pmab-specific-posts-exclude' ).hide();
			$( '#_pmab_meta_specific_post_exclude' ).val( '' );
		}
	} );

	fetch( pmabProps.adminAjax + '?action=pmab_posts' )
		.then( resp => resp.json() )
		.then( posts => pmabProps.allPosts = posts )
		.then( () => {
			$injType.change();
		} );
} );