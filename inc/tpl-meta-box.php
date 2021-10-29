<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script type="text/JavaScript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
	#pmab_metabox select,
	#pmab_metabox input {
		box-sizing: border-box;
		width: 100%;
	}

	#pmab_metabox select,
	#pmab_metabox input:not([type='range']) {
		border: 1px solid #757575;
	}

	#pmab_metabox .select2-container {
		width: 100% !important;
	}

	#pmab_metabox .select2-selection--multiple .select2-selection__rendered,
	#pmab_metabox .select2-search--inline textarea.select2-search__field {
		/*display: inline-block;*/
		/*margin: 0;*/
		/*vertical-align: middle;*/
	}

	span.select2-search.select2-search--inline {
		min-width: 7%;
		display: inline-block;
		margin-bottom: .1em;
	}

	.pmab-info-text {
		font-size: 9px;
	}

	#pmab_metabox .select2-search--inline textarea.select2-search__field {
		/*margin-left: 4px;*/
		/*min-width: 10px;*/
	}

	#pmab_metabox .select2-selection__choice {
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		max-width: calc(100% - 1em);
		margin: 5px 0 0 5px;
	}

	#pmab_metabox ul.select2-selection__rendered {
		display: inline;
	}

	#_pmab_meta_priority_labels {
		display: flex;
		justify-content: space-between;
		text-transform: uppercase;
		font-size: .7em;
		opacity: .7;
		padding: .7em 0 0;
	}

	#_pmab_meta_priority_labels option[label] {
		flex: 50px 0;
		text-align: center;
	}

	.pmab-section-title {
		font-size: 16px;
		font-weight: bold;
		display: block;
		margin: 0.5em 0;
	}

	.pmab-shortcode-dl {
		line-height: 1.6
	}

	.pmab-shortcode-dl dt {
		font-family: monospace;
	}

	.pmab-shortcode-dl dd {
		margin: 0 .1em .5em;
	}
</style>
<div id="pmab_metabox">
	<div class="" style="padding-bottom:1rem;">
		<label class="pmab-section-title" for="_pmab_meta_type"><?php _e( 'Location', 'pmab' ); ?></label>

		<select name="_pmab_meta_type" id="_pmab_meta_type">
			<option value="post_page" selected>Entire Website</option>
			<option disabled style="font-weight: bolder;">Post</option>
			<option value="all_post" <?php selected( $_pmab_meta_type, 'all_post' ); ?>>All Posts
			</option>
			<option value="post" <?php selected( $_pmab_meta_type, 'post' ); ?>>Specific Posts</option>
			<option value="category" <?php selected( $_pmab_meta_type, 'category' ); ?>>Posts By
				Category
			</option>
			<option value="tags" <?php selected( $_pmab_meta_type, 'tags' ); ?>>Posts By Tag</option>
			<option disabled style="font-weight: bolder;"> Pages</option>
			<option value="all_page" <?php selected( $_pmab_meta_type, 'all_page' ); ?>>All Pages
			</option>
			<option value="page" <?php selected( $_pmab_meta_type, 'page' ); ?>>Specific Page</option>
			<?php if ( class_exists( 'wooCommerce' ) ) { ?>
				<option disabled style="font-weight: bolder;"> WooCommerce</option>
				<option value="woo_all_pages" <?php selected( $_pmab_meta_type, 'woo_all_pages' ); ?>>All WooCommerce Pages
				</option>
				<option value="woo_all_products" <?php selected( $_pmab_meta_type, 'woo_all_products' ); ?>>All Products
				</option>
				<option value="woo_all_products_in_stock" <?php selected( $_pmab_meta_type, 'woo_all_products_in_stock' ); ?>>
					Products in stock
				</option>
				<option value="woo_all_products_out_of_stock" <?php selected( $_pmab_meta_type, 'woo_all_products_out_of_stock' ); ?>>
					Products out of stock
				</option>
				<option value="woo_all_products_on_backorder" <?php selected( $_pmab_meta_type, 'woo_all_products_on_backorder' ); ?>>
					Products on backorder
				</option>
				<option value="woo_all_products_on_sale" <?php selected( $_pmab_meta_type, 'woo_all_products_on_sale' ); ?>>
					Products on sale
				</option>
				<option value="woo_product" <?php selected( $_pmab_meta_type, 'woo_product' ); ?>>Specific Product</option>
				<option value="woo_pro_category" <?php selected( $_pmab_meta_type, 'woo_pro_category' ); ?>>Products by Category
				</option>
				<option value="woo_pro_tags" <?php selected( $_pmab_meta_type, 'woo_pro_tags' ); ?>>Products by Tag</option>
				<option value="woo_all_category_pages" <?php selected( $_pmab_meta_type, 'woo_all_category_pages' ); ?>>All
					Category Pages
				</option>
				<option value="woo_category_page" <?php selected( $_pmab_meta_type, 'woo_category_page' ); ?>>Specific Category
					Page
				</option>
				<option value="woo_shop" <?php selected( $_pmab_meta_type, 'woo_shop' ); ?>>Shop Page</option>
				<option value="woo_account" <?php selected( $_pmab_meta_type, 'woo_account' ); ?>>My Account Page</option>
				<option value="woo_basket" <?php selected( $_pmab_meta_type, 'woo_basket' ); ?>>Basket Page</option>
				<option value="woo_checkout" <?php selected( $_pmab_meta_type, 'woo_checkout' ); ?>>Checkout Page</option>
			<?php } ?>

		</select>
	</div>

	<div class="category-box"
			 style="<?php echo $_pmab_meta_category == '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_category[]"><?php _e( 'Categories', 'pmab' ); ?></label>
		<?php $_pmab_meta_category = explode( ',', str_replace( ' ', '', $_pmab_meta_category ) ); ?>
		<select class="pmab-multi-select" name="_pmab_meta_category[]" multiple id="_pmab_meta_category[]"
						class="postbox">
			<?php
			foreach ( $_pmab_categories as $category ):
				$cat_ID = $category->cat_ID;
				?>
				<option <?php selected( in_array( $cat_ID, $_pmab_meta_category ) ) ?> value="<?php echo $cat_ID ?>">
					<?php echo $category->name ?></option>
			<?php
			endforeach; ?>
		</select>
	</div>
	<div class="woo-category-box"

			 style="<?php echo $_pmab_meta_woo_category == '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_woo_category"><?php _e( 'Product Categories', 'pmab' ); ?></label>
		<?php $_pmab_meta_woo_category = explode( ',', str_replace( ' ', '', $_pmab_meta_woo_category ) ); ?>
		<select class="pmab-multi-select" name="_pmab_meta_woo_category[]" id="_pmab_meta_woo_category" multiple>
			<?php
			foreach ( $_pmab_woo_categories as $category ):
				$cat_ID = $category->cat_ID;
				?>
				<option <?php selected( in_array( $cat_ID, $_pmab_meta_woo_category ) ) ?> value="<?php echo $cat_ID ?>">
					<?php echo $category->name ?></option>
			<?php
			endforeach; ?>
		</select>
	</div>
	<div class="pmab-specific-posts"
			 style="<?php echo $_pmab_meta_specific_post == '' ? 'display: none;' : ''; ?> padding-bottom:1rem ">
		<label for="_pmab_meta_specific_post"><?php _e( 'Select', 'pmab' ); ?></label>
		<select type="text" id="_pmab_meta_specific_post" multiple name="_pmab_meta_specific_post[]"
						class="pmab-multi-select" data-value="<?php echo $_pmab_meta_specific_post ?>">
			<?php ?>
		</select>
	</div>
	<div class="specificwoocategory">
		<label for="_pmab_meta_specific_woocategory"><?php _e( 'Product categories', 'pmab' ); ?></label>
		<?php $_pmab_meta_specific_woocategory = explode( ',', str_replace( ' ', '', $_pmab_meta_specific_woocategory ) ); ?>
		<select class="pmab-multi-select" name="_pmab_meta_specific_woocategory[]" multiple id="_pmab_meta_specific_woocategory">
			<?php
			foreach ( $_pmab_woo_categories as $category ):
				$cat_ID = $category->cat_ID;
				?>
				<option <?php selected( in_array( $cat_ID, $_pmab_meta_specific_woocategory ) ) ?> value="<?php echo $cat_ID ?>">
				<?php echo $category->name ?></option>
			<?php
			endforeach; ?>
		</select>
	</div>
	<div class="tags" style="<?php echo $_pmab_meta_tags === '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_tags"><?php _e( 'Tag IDs', 'pmab' ); ?> <span
				class="pmab-info-text">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_tags" name="_pmab_meta_tags"
					 value="<?php echo esc_attr( $_pmab_meta_tags ); ?>" size="25"/>

	</div>

	<div style="padding-bottom:1rem;">
		<label class="pmab-section-title" for="_pmab_meta_tag_n_fix"><?php _e( 'Position', 'pmab' ); ?></label>
		<select name="_pmab_meta_tag_n_fix" id="_pmab_meta_tag_n_fix" class="postbox col-12">
			<option value="" disabled style="font-weight: bolder;">Position</option>
			<option value="top_before" <?php selected( $_pmab_meta_tag_n_fix, 'top_before' ); ?>>Top
			</option>
			<option value="bottom_after" <?php selected( $_pmab_meta_tag_n_fix, 'bottom_after' ); ?>>
				Bottom
			</option>
			<option class="pmab-product-options" value="before_add_to_cart_form" <?php selected( $_pmab_meta_tag_n_fix, 'before_add_to_cart_form' ); ?>>
				Before add to cart
			</option>
			<option class="pmab-product-options" value="after_add_to_cart_form" <?php selected( $_pmab_meta_tag_n_fix, 'after_add_to_cart_form' ); ?>>
				After add to cart
			</option>
			<option class="pmab-product-options" value="product_tabs" <?php selected( $_pmab_meta_tag_n_fix, 'product_tabs' ); ?>>
				Before product tabs
			</option>
			<option class="pmab-product-options" value="product_after_tabs" <?php selected( $_pmab_meta_tag_n_fix, 'product_after_tabs' ); ?>>
				After product tabs
			</option>
			<option class="pmab-product-options" value="product_meta_start" <?php selected( $_pmab_meta_tag_n_fix, 'product_meta_start' ); ?>>
				Before category text
			</option>
			<option class="pmab-product-options" value="product_meta_end" <?php selected( $_pmab_meta_tag_n_fix, 'product_meta_end' ); ?>>
				After category text
			</option>
			<option class="pmab-no-woo" value="h2_after" <?php selected( $_pmab_meta_tag_n_fix, 'h2_after' ); ?>>After
				Heading
			</option>
			<option class="pmab-no-woo" value="p_after" <?php selected( $_pmab_meta_tag_n_fix, 'p_after' ); ?>>After
				Blocks
			</option>
		</select>
	</div>

	<div class="certain_num"
			 style=" <?php echo $_pmab_meta_number_of_blocks === '' ? 'display: none;' : ''; ?> padding-bottom:1rem"><label
			for="_pmab_meta_number_of_blocks">
			<?php _e( 'After Certain Number', 'pmab' ); ?>
		</label>
		<input type="number" id="_pmab_meta_number_of_blocks" name="_pmab_meta_number_of_blocks"
					 value="<?php echo esc_attr( $_pmab_meta_number_of_blocks ); ?>" size="25"/>
	</div>

	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_priority">
			<?php _e( 'Priority', 'pmab' ); ?>
			<span class="pmab-info-text">Small priority values show first</span>
		</label>

		<input type="range" value="<?php echo esc_attr( $_pmab_meta_priority ); ?>" list="_pmab_meta_priority_labels"
					 min="-25" max="25" step="5" name="_pmab_meta_priority" id="_pmab_meta_priority" class="col-12">

		<datalist id="_pmab_meta_priority_labels">
			<option value="-25" label="Show first"></option>
			<option value="-20"></option>
			<option value="-15"></option>
			<option value="-10"></option>
			<option value="-5"></option>
			<option value="0" label="|"></option>
			<option value="5"></option>
			<option value="10"></option>
			<option value="15"></option>
			<option value="20"></option>
			<option value="25" label="Show last"></option>
		</datalist>
	</div>

	<div class="pmab-specific-posts-exclude"
			 style="padding-bottom:1rem">
		<label for="_pmab_meta_specific_post_exclude"><?php _e( 'Post IDs to exclude', 'pmab' ); ?> <span
				class="pmab-info-text">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_specific_post_exclude" name="_pmab_meta_specific_post_exclude"
					 value="<?php echo esc_attr( $_pmab_meta_specific_post_exclude ); ?>" size="25"/>
	</div>


	<label class="pmab-section-title" for="_pmab_meta_type"><?php _e( 'Scheduling', 'pmab' ); ?></label>

	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_startdate"><?php _e( 'Select Start Date', 'pmab' ); ?></label>
		<input type="datetime-local" id="_pmab_meta_startdate" name="_pmab_meta_startdate"
					 value="<?php echo esc_attr( $_pmab_meta_startdate ); ?>" size="25"/>
	</div>


	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_expiredate"><?php _e( 'Select Expiry Date', 'pmab' ); ?></label>
		<input type="datetime-local" id="_pmab_meta_expiredate" name="_pmab_meta_expiredate"
					 value="<?php echo esc_attr( $_pmab_meta_expiredate ); ?>" size="25"/>
	</div>

	<div style="padding-bottom:1rem;">
		<label class="pmab-section-title" for="_pmab_responsive_visibility"><?php _e( 'Show on devices', 'pmab' ); ?></label>
		<select name="_pmab_responsive_visibility" id="_pmab_responsive_visibility" class="postbox col-12">
			<option value="" <?php selected( $_pmab_responsive_visibility, '' ); ?>>
				All devices
			</option>
			<option value="desktop" <?php selected( $_pmab_responsive_visibility, 'desktop' ); ?>>
				Desktop only
			</option>
			<option value="mobile" <?php selected( $_pmab_responsive_visibility, 'mobile' ); ?>>
				Mobile only
			</option>
		</select>
	</div>

	<div>
		<div class="pmab-section-title"><?php _e( 'Dynamic content', 'pmab' ); ?></div>
		<p>Use these shortcodes to add dynamic post content in your injected blocks.</p>
		<dl class="pmab-shortcode-dl">
			<dt>[ib_title]</dt>
			<dd>Shows current post title</dd>
			<dt>[ib_price]</dt>
			<dd>Shows product price</dd>
			<dt>[ib_product_categories]</dt>
			<dd>Shows product categories</dd>
			<dt>[ib_stock]</dt>
			<dd>Shows product stock</dd>
		</dl>
	</div>

</div>
