<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<script type="text/JavaScript" src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<style>
	#pmab_metabox select, #pmab_metabox input {
		box-sizing: border-box;
		border: 1px solid #757575;
		width: 100%;
	}

	#pmab_metabox .select2-container {
		width: 100% !important;
	}
</style>
<div id="pmab_metabox">
	<div class="" style="padding-bottom:1rem;">
		<label for="_pmab_meta_type"><b style="font-size:16px"><?php _e( 'Location', 'pmab' ); ?></b></label>
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
				<option value="woo_pro_category" <?php selected( $_pmab_meta_type, 'woo_pro_category' ); ?>>Products by
					Category
				</option>
				<option value="woo_pro_tags" <?php selected( $_pmab_meta_type, 'woo_pro_tags' ); ?>>Products by Tag</option>
				<option value="woo_product" <?php selected( $_pmab_meta_type, 'woo_product' ); ?>>Specific Product</option>
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
		<select name="_pmab_meta_woo_category" id="_pmab_meta_woo_category">
			<option disabled selected>Select Category</option>
			<?php
			foreach ( $_pmab_woo_categories as $category ):
				$cat_ID = $category->cat_ID;
				?>
				<option value="<?php echo $cat_ID ?>" <?php selected( $_pmab_meta_woo_category, $cat_ID ); ?>>
					<?php echo $category->name ?></option>
			<?php
			endforeach; ?>
		</select>
	</div>
	<div class="specificpost"
			 style="<?php echo $_pmab_meta_specific_post == '' ? 'display: none;' : ''; ?> padding-bottom:1rem ">
		<label for="_pmab_meta_specific_post"><?php _e( 'IDs', 'pmab' ); ?> <span
				style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_specific_post" name="_pmab_meta_specific_post"
					 value="<?php echo esc_attr( $_pmab_meta_specific_post ); ?>" size="25"/>

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
				style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_tags" name="_pmab_meta_tags"
					 value="<?php echo esc_attr( $_pmab_meta_tags ); ?>" size="25"/>

	</div>

	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_tag_n_fix"><b style="font-size:16px"><?php _e( 'Position', 'pmab' ); ?></b></label>
		<select name="_pmab_meta_tag_n_fix" id="_pmab_meta_tag_n_fix" class="postbox col-12">
			<option value="" disabled style="font-weight: bolder;">Position</option>
			<option value="top_before" <?php selected( $_pmab_meta_tag_n_fix, 'top_before' ); ?>>Top
			</option>
			<option value="bottom_after" <?php selected( $_pmab_meta_tag_n_fix, 'bottom_after' ); ?>>
				Bottom
			</option>
			<option style="display:none;" value="woo_hook" <?php selected( $_pmab_meta_tag_n_fix, 'woo_hook' ); ?>>Custom
				Hooks
			</option>
			<option class="pmab-no-woo" value="h2_after" <?php selected( $_pmab_meta_tag_n_fix, 'h2_after' ); ?>>After
				Heading
			</option>
			<option class="pmab-no-woo" value="p_after" <?php selected( $_pmab_meta_tag_n_fix, 'p_after' ); ?>>After
				Blocks
			</option>
		</select>
	</div>

	<div class="woo_hook" style="<?php echo $_pmab_meta_hook == '' ? 'display: none;' : ''; ?> padding-bottom:1rem;">
		<label for="_pmab_meta_hook"><b style="font-size:16px"><?php _e( 'Select Hooks', 'pmab' ); ?></b></label>
		<select name="_pmab_meta_hook" id="_pmab_meta_hook" class="postbox col-12">
			<option value="" style="font-weight: bolder;">Hooks</option>
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
	<div class="specificpost_exclude"
			 style="padding-bottom:1rem">
		<label for="_pmab_meta_specific_post_exclude"><?php _e( 'Post IDs to exclude', 'pmab' ); ?> <span
				style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_specific_post_exclude" name="_pmab_meta_specific_post_exclude"
					 value="<?php echo esc_attr( $_pmab_meta_specific_post_exclude ); ?>" size="25"/>
	</div>
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
</div>
