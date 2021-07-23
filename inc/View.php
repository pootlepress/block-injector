<div>
	<div class="" style="padding-bottom:1rem;">
		<label for="_pmab_meta_type"><b style="font-size:16px"><?php _e('Location', 'pmab'); ?></b></label>
		<select name="_pmab_meta_type" id="_pmab_meta_type" class="postbox">
			<option value="post_page" selected>Entire Website</option>
			<option disabled style="font-weight: bolder;">Post</option>
			<option value="all_post" <?php echo pmab_select_checker($_pmab_meta_type, 'all_post'); ?>>All Posts
			</option>
			<option value="post" <?php echo pmab_select_checker($_pmab_meta_type, 'post'); ?>>Specific Posts</option>
			<option value="category" <?php echo pmab_select_checker($_pmab_meta_type, 'category'); ?>>Posts By
				Category
			</option>
			<option value="tags" <?php echo pmab_select_checker($_pmab_meta_type, 'tags'); ?>>Posts By Tag</option>
			<option disabled style="font-weight: bolder;"> Pages</option>
			<option value="all_page" <?php echo pmab_select_checker($_pmab_meta_type, 'all_page'); ?>>All Pages
			</option>
			<option value="page" <?php echo pmab_select_checker($_pmab_meta_type, 'page'); ?>>Specific Page</option>
			<?php if( class_exists( 'wooCommerce' ) ) { ?>
			<option disabled style="font-weight: bolder;"> WooCommerce</option>
			<option value="woo_all_pages" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_all_pages'); ?>>All WooCommerce Pages</option>
			<option value="woo_all_products" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_all_products'); ?>>All Products</option>
			<option value="woo_pro_category" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_pro_category'); ?>>Products by Category</option>
			<option value="woo_tags" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_tags'); ?>>Products by Tag</option>
			<option value="woo_product" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_product'); ?>>Specific Product</option>
			<option value="woo_all_category_pages" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_all_category_pages'); ?>>All Category Pages</option>
			<option value="woo_category_page" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_category_page'); ?>>Specific Category Page</option>
			<option value="woo_shop" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_shop'); ?>>Shop Page</option>
			<option value="woo_account" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_account'); ?>>My Account Page</option>
			<option value="woo_basket" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_basket'); ?>>Basket Page</option>
			<option value="woo_checkout" <?php echo pmab_select_checker($_pmab_meta_type, 'woo_checkout'); ?>>Checkout Page</option>
			<?php } ?>

		</select>


	</div>
	<div class="category-box"
	
		 style="<?php echo $_pmab_meta_category == '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_category"><?php _e('Categories', 'pmab'); ?></label>
		<select name="_pmab_meta_category" id="_pmab_meta_category" class="postbox">
			<option disabled selected style="font-weight: bolder;">Select Category </option>
			<?php
            foreach ($_pmab_categories as $category):
                ?>
				<option value="<?php echo $category->cat_ID ?>" <?php echo pmab_select_checker($_pmab_meta_category, $category->cat_ID); ?>><?php echo $category->name ?></option>
			<?php
            endforeach; ?>
		</select>
	</div>
	<div class="woo-category-box"
	
		 style="<?php echo $_pmab_meta_woo_category == '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_woo_category"><?php _e('Product Categories', 'pmab'); ?></label>
		<select name="_pmab_meta_woo_category" id="_pmab_meta_woo_category" class="postbox">
			<option disabled selected style="font-weight: bolder;">Select Category </option>
			<?php
            foreach ($_pmab_woo_categories as $category):
                ?>
				<option value="<?php echo $category->cat_ID ?>" <?php echo pmab_select_checker($_pmab_meta_woo_category, $category->cat_ID); ?>><?php echo $category->name ?></option>
			<?php
            endforeach; ?>
		</select>
	</div>
	<div class="specificpost"
		 style="<?php echo $_pmab_meta_specific_post == '' ? 'display: none;' : ''; ?> padding-bottom:1rem ">
		<label for="_pmab_meta_specific_post"><?php _e('IDs', 'pmab'); ?> <span
					style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_specific_post" name="_pmab_meta_specific_post"
			   value="<?php echo esc_attr($_pmab_meta_specific_post); ?>" size="25" class="postbox"/>

	</div>
	<div class="specificwoocategory"
		 style="<?php echo $_pmab_meta_specific_woocategory === '' ? 'display: none;' : ''; ?> padding-bottom:1rem ">
		<label for="_pmab_meta_specific_woocategory"><?php _e('IDs', 'pmab'); ?> <span
					style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_specific_woocategory" name="_pmab_meta_specific_woocategory"
			   value="<?php echo esc_attr($_pmab_meta_specific_woocategory); ?>" size="25" class="postbox"/>

	</div>
	<div class="tags" style="<?php echo $_pmab_meta_tags === '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_tags"><?php _e('Tag IDs', 'pmab'); ?> <span
					style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_tags" name="_pmab_meta_tags"
			   value="<?php echo esc_attr($_pmab_meta_tags); ?>" size="25" class="postbox"/>

	</div>
	
	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_tag_n_fix"><b style="font-size:16px"><?php _e('Position', 'pmab'); ?></b></label>
		<select name="_pmab_meta_tag_n_fix" id="_pmab_meta_tag_n_fix" class="postbox col-12">
			<option value="" disabled style="font-weight: bolder;">Position</option>
			<option value="top_before" <?php echo pmab_select_checker($_pmab_meta_tag_n_fix, 'top_before'); ?>>Top
			</option>
			<option value="bottom_after" <?php echo pmab_select_checker($_pmab_meta_tag_n_fix, 'bottom_after'); ?>>
				Bottom
			</option>
			<option value="woo_hook" <?php echo pmab_select_checker($_pmab_meta_tag_n_fix, 'woo_hook'); ?>>Custom Hooks</option>
			<option value="h2_after" <?php echo pmab_select_checker($_pmab_meta_tag_n_fix, 'h2_after'); ?>>After
				Heading
			</option>
			<option value="p_after" <?php echo pmab_select_checker($_pmab_meta_tag_n_fix, 'p_after'); ?>>After
				Blocks
			</option>
		</select>
	</div>

	<div class="woo_hook" style="<?php echo $_pmab_meta_hook == '' ? 'display: none;' : ''; ?> padding-bottom:1rem;">
		<label for="_pmab_meta_hook"><b style="font-size:16px"><?php _e('Select Hooks', 'pmab'); ?></b></label>
		<select name="_pmab_meta_hook" id="_pmab_meta_hook" class="postbox col-12">
			<option value="" style="font-weight: bolder;">Hooks</option>
			<?php
			 	require('WooCommerce_Hooks.php');
			?>
		</select>
	</div>

	
	<div class="certain_num"
		 style=" <?php echo $_pmab_meta_number_of_blocks === '' ? 'display: none;' : ''; ?> padding-bottom:1rem"><label
				for="_pmab_meta_number_of_blocks">
			<?php _e('After Certain Number', 'pmab'); ?>
		</label>
		<input type="number" id="_pmab_meta_number_of_blocks" name="_pmab_meta_number_of_blocks"
			   value="<?php echo esc_attr($_pmab_meta_number_of_blocks); ?>" size="25" class="postbox"/>
	</div>
	<div style="padding-bottom:1rem;">
		<select name="" id="" class="postbox " style="display:none">
			<option value="" selected>Exclude</option>
		</select>
	</div>
	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_type2"><b
					style="font-size:16px"><?php _e('Exclude Post & Pages', 'pmab'); ?></b></label>
		<select name="_pmab_meta_type2" id="_pmab_meta_type2" class="postbox">
			<option value="" style="font-weight: bolder;">Exclude Option</option>
			<option value="post_exclude" <?php echo pmab_select_checker($_pmab_meta_type2, 'post_exclude'); ?>>
				Specific Posts
			</option>
			<option value="post_exclude" <?php echo pmab_select_checker($_pmab_meta_type2, 'post_exclude'); ?>>
				Specific Products
			</option>
			<option value="page_exclude" <?php echo pmab_select_checker($_pmab_meta_type2, 'page_exclude'); ?>>
				Specific Pages
			</option>
		</select>
	</div>
	<div class="specificpost_exclude"
		 style="<?php echo $_pmab_meta_specific_post_exclude === '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_specific_post_exclude"><?php _e('IDs', 'pmab'); ?> <span style="font-size:8px;">Comma Seperated</span></label>
		<input type="text" id="_pmab_meta_specific_post_exclude" name="_pmab_meta_specific_post_exclude"
			   value="<?php echo esc_attr($_pmab_meta_specific_post_exclude); ?>" size="25" class="postbox"/>
	</div>
</div>
<div style="padding-bottom:1rem;">
	<label for="_pmab_meta_startdate"><?php _e('Select Start Date', 'pmab'); ?></label>
	<input type="datetime-local" id="_pmab_meta_startdate" name="_pmab_meta_startdate"
		   value="<?php echo esc_attr($_pmab_meta_startdate); ?>" size="25" class="postbox"/>
</div>


<div style="padding-bottom:1rem;">
	<label for="_pmab_meta_expiredate"><?php _e('Select Expiry Date', 'pmab'); ?></label>
	<input type="datetime-local" id="_pmab_meta_expiredate" name="_pmab_meta_expiredate"
		   value="<?php echo esc_attr($_pmab_meta_expiredate); ?>" size="25" class="postbox"/>
</div>
