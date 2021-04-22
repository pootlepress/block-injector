<div>
	<div class="" style="padding-bottom:1rem;">
		<label for="_pmab_meta_type"><b style="font-size:16px"><?php _e( 'Location', 'pmab' ); ?></b></label>
		<select name="_pmab_meta_type" id="_pmab_meta_type" class="postbox">
			<option value="post_page" selected><?php _e( 'Entire Website', 'pmab' ); ?></option>
			<option disabled style="font-weight: bolder;"><?php _e( 'Post', 'pmab' ); ?></option>
			<option value="all_post" <?php echo pmab_select_checker( $data['_pmab_meta_type'], 'all_post' ); ?>>
				<?php _e( 'All Posts', 'pmab' ); ?>
			</option>
			<option value="post" <?php echo pmab_select_checker( $data['_pmab_meta_type'], 'post' ); ?>>
				<?php _e( 'Specific Posts', 'pmab' ); ?>
				</option>
			<option value="category" <?php echo pmab_select_checker( $data['_pmab_meta_type'], 'category' ); ?>>
			<?php _e( 'Category', 'pmab' ); ?>

			</option>
			<option value="tags" <?php echo pmab_select_checker( $data['_pmab_meta_type'], 'tags' ); ?>><?php _e( 'Posts By Tag', 'pmab' ); ?></option>
			<option disabled style="font-weight: bolder;"> Pages</option>
			<option value="all_page" <?php echo pmab_select_checker( $data['_pmab_meta_type'], 'all_page' ); ?>>
				<?php _e( 'All Pages', 'pmab' ); ?>
			</option>
			<option value="page" <?php echo pmab_select_checker( $data['_pmab_meta_type'], 'page' ); ?>>
				<?php _e( 'Specific Page', 'pmab' ); ?>
				</option>
		</select>


	</div>
	<div class="category-box"
		 style="<?php echo $data['_pmab_meta_category'] === '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_category"><?php _e( 'Categories', 'pmab' ); ?></label>
		<select name="_pmab_meta_category" id="_pmab_meta_category" class="postbox">
			<option disabled style="font-weight: bolder;"><?php _e( 'Select Category', 'pmab' ); ?></option>
			<?php
			foreach ( $data['_pmab_categories'] as $category ) :
				?>
				<option value="<?php echo $category->cat_ID; ?>" <?php echo pmab_select_checker( $data['_pmab_meta_category'], $category->cat_ID ); ?>>
					<?php _e( $category->name, 'pmab' ); ?></option>
				<?php
			endforeach;
			?>
		</select>
	</div>
	<div class="specificpost"
		 style="<?php echo $data['_pmab_meta_specific_post'] === '' ? 'display: none;' : ''; ?> padding-bottom:1rem ">
		<label for="_pmab_meta_specific_post"><?php _e( 'IDs', 'pmab' ); ?> <span
					style="font-size:8px;"><?php _e( 'Comma Separated', 'pmab' ); ?></span></label>
		<input type="text" id="_pmab_meta_specific_post" name="_pmab_meta_specific_post"
			   value="<?php echo esc_attr( $data['_pmab_meta_specific_post'] ); ?>" size="25" class="postbox"/>

	</div>
	<div class="tags" style="<?php echo $data['_pmab_meta_tags'] === '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_tags"><?php _e( 'Tag IDs', 'pmab' ); ?> <span
					style="font-size:8px;"><?php _e( 'Comma Separated', 'pmab' ); ?></span></label>
		<input type="text" id="_pmab_meta_tags" name="_pmab_meta_tags"
			   value="<?php echo esc_attr( $data['_pmab_meta_tags'] ); ?>" size="25" class="postbox"/>

	</div>
	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_tag_n_fix"><b style="font-size:16px"><?php _e( 'Position', 'pmab' ); ?></b></label>
		<select name="_pmab_meta_tag_n_fix" id="_pmab_meta_tag_n_fix" class="postbox col-12">
			<option value="" disabled style="font-weight: bolder;"><?php _e( 'Position', 'pmab' ); ?></option>
			<option value="top_before" <?php echo pmab_select_checker( $data['_pmab_meta_tag_n_fix'], 'top_before' ); ?>>
				<?php _e( 'Top', 'pmab' ); ?>

			</option>
			<option value="bottom_after" <?php echo pmab_select_checker( $data['_pmab_meta_tag_n_fix'], 'bottom_after' ); ?>>
				<?php _e( 'Bottom', 'pmab' ); ?>
			</option>
			<option value="h2_after" <?php echo pmab_select_checker( $data['_pmab_meta_tag_n_fix'], 'h2_after' ); ?>>After
				Heading
			</option>
			<option value="p_after" <?php echo pmab_select_checker( $data['_pmab_meta_tag_n_fix'], 'p_after' ); ?>>After
				Blocks
			</option>
		</select>
	</div>
	<div class="certain_num"
		 style=" <?php echo $data['_pmab_meta_number_of_blocks'] === '' ? 'display: none;' : ''; ?> padding-bottom:1rem"><label
				for="_pmab_meta_number_of_blocks">
			<?php _e( 'After Certain Number', 'pmab' ); ?>
		</label>
		<input type="number" id="_pmab_meta_number_of_blocks" name="_pmab_meta_number_of_blocks"
			   value="<?php echo esc_attr( $data['_pmab_meta_number_of_blocks'] ); ?>" size="25" class="postbox"/>
	</div>
	<div style="padding-bottom:1rem;">
		<select name="" id="" class="postbox " style="display:none">
			<option value="" selected><?php _e( 'Exclude', 'pmab' ); ?></option>
		</select>
	</div>
	<div style="padding-bottom:1rem;">
		<label for="_pmab_meta_type2"><b
					style="font-size:16px"><?php _e( 'Exclude Post & Pages', 'pmab' ); ?></b></label>
		<select name="_pmab_meta_type2" id="_pmab_meta_type2" class="postbox">
			<option value="" style="font-weight: bolder;"><?php _e( 'Exclude Option', 'pmab' ); ?></option>
			<option value="post_exclude" <?php echo pmab_select_checker( $data['_pmab_meta_type2'], 'post_exclude' ); ?>>
				<?php _e( 'Specific Posts', 'pmab' ); ?>

			</option>
			<option value="page_exclude" <?php echo pmab_select_checker( $data['_pmab_meta_type2'], 'page_exclude' ); ?>>
				<?php _e( 'Specific Pages', 'pmab' ); ?>
			</option>
		</select>
	</div>
	<div class="specificpost_exclude"
		 style="<?php echo $data['_pmab_meta_specific_post_exclude'] === '' ? 'display: none;' : ''; ?> padding-bottom:1rem">
		<label for="_pmab_meta_specific_post_exclude"><?php _e( 'IDs', 'pmab' ); ?> <span style="font-size:8px;"><?php _e( 'Comma Separated', 'pmab' ); ?></span></label>
		<input type="text" id="_pmab_meta_specific_post_exclude" name="_pmab_meta_specific_post_exclude"
			   value="<?php echo esc_attr( $data['_pmab_meta_specific_post_exclude'] ); ?>" size="25" class="postbox"/>
	</div>
</div>
<div style="padding-bottom:1rem;">
	<label for="_pmab_meta_startdate"><?php _e( 'Select Start Date', 'pmab' ); ?></label>
	<input type="datetime-local" id="_pmab_meta_startdate" name="_pmab_meta_startdate"
		   value="<?php echo esc_attr( $data['_pmab_meta_startdate'] ); ?>" size="25" class="postbox"/>
</div>


<div style="padding-bottom:1rem;">
	<label for="_pmab_meta_expiredate"><?php _e( 'Select Expiry Date', 'pmab' ); ?></label>
	<input type="datetime-local" id="_pmab_meta_expiredate" name="_pmab_meta_expiredate"
		   value="<?php echo esc_attr( $data['_pmab_meta_expiredate'] ); ?>" size="25" class="postbox"/>
</div>
