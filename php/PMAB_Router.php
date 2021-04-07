<?php

/**
 * Router class.
 *
 * @package BlockInjector
 */
if (!class_exists('PMAB_Router')) {
	/**
	 * Plugin Router.
	 */
	class PMAB_Router
	{

		private $post_type = 'block_injector';

		/**
		 * Plugin interface.
		 *
		 * @var Plugin
		 */
		protected $plugin;

		/**
		 * Setup the plugin instance.
		 *
		 * @param Plugin $plugin Instance of the plugin abstraction.
		 */
		public function __construct($plugin)
		{
			$this->plugin = $plugin;
			$this->manual_hook();
		}

		/**
		 * Hook into WP.
		 *
		 * @return void
		 */
		public function init()
		{
			add_action('init', array($this, 'register_post_type'));
			add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
			add_action('add_meta_boxes', array($this, 'add_meta_box'));
			add_action('save_post', array($this, 'save_post'));
			// add_filter( 'the_content', array( $this, 'test' ), 1 );
		}

		public function manual_hook()
		{
			// code...
			$posts = get_posts(
				array(
					'post_type'      => $this->post_type,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				)
			);

			// loop over each post
			foreach ($posts as $p) {

				$id = $p->ID;
				// get the meta you need form each post_pmab_meta_specific_post
				$num_of_blocks        = get_post_meta($id, '_pmab_meta_number_of_blocks', true);
				$specific_post        = get_post_meta($id, '_pmab_meta_specific_post', true);
				$specific_post2       = get_post_meta($id, '_pmab_meta_specific_post2', true);
				$tags       		  = get_post_meta($id, '_pmab_meta_tags', true);
				$tag_type             = get_post_meta($id, '_pmab_meta_tag_n_fix', true);
				$inject_content_type  = get_post_meta($id, '_pmab_meta_type', true);
				$inject_content_type2 = get_post_meta($id, '_pmab_meta_type2', true);
				$startdate            = get_post_meta($id, '_pmab_meta_startdate', true);
				$expiredate           = get_post_meta($id, '_pmab_meta_expiredate', true);
				$category             = get_post_meta($id, '_pmab_meta_category', true);


				$tag_type = explode('_', $tag_type);
				if (!empty($tag_type) && isset($tag_type[0]) && isset($tag_type[1])) {

					$tag          = $tag_type[0];
					$after_before = $tag_type[1];

					add_filter(
						'the_content',
						function ($content) use ($inject_content_type, $inject_content_type2, $p, $tag, $num_of_blocks, $after_before, $expiredate, $startdate, $specific_post, $specific_post2, $category, $id, $tags) {
							if ($tag == 'top') {
								$num_of_blocks = 0;
							} else if ($tag == 'bottom') {
								$num_of_blocks = PHP_INT_MAX;
							}

							// Check if we're inside the main loop in a single Post.

							$date = date('Y-m-d\TH:i', time()); // Date object using current date and time
							$currentdate   = $date;


							$expirydate = $expiredate;
							$startingdate = $startdate;

							if (($inject_content_type == 'tags' && ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate))) && is_single()) {
								$all_tags = get_tags();
								$tag_id = array();
								foreach (explode(',', $tags) as $tags) {
									$tag_id[] = $tags;
								}

								foreach (explode(',', $tags) as $tags) {

									$args = array(
										'tag__in' => $tag_id
									);

									$is_post = get_posts($args);
									if ($is_post) {
										foreach ($is_post as $is_posts) {
											if (($inject_content_type == 'tags' && (($startingdate == '' || $expirydate == '') || ($expirydate >= $currentdate && $startingdate <= $currentdate))) && is_single($is_posts->ID)) {
												if ($inject_content_type2 == 'post2') {
													$thisposts = array();
													foreach (explode(',', $specific_post2) as $specific_post_id) {
														$thisposts[] = $specific_post_id;
													}


													$is_post = get_post();

													if (!in_array($is_posts->ID, $thisposts)) {

														return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
													}
												} else {
													return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
												}
											}
										}
									}
								}
							}
							if (($inject_content_type == 'category' && ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate))) && is_single()) {

								$is_post = get_post();
								$categories = wp_get_post_categories($is_post->ID);

								for ($i = 0; $i < count($categories); $i++) {
									if ($categories[$i] == $category) {

										if ($inject_content_type2 == 'post2') {
											$thisposts = array();
											foreach (explode(',', $specific_post2) as $specific_post_id) {
												$thisposts[] = $specific_post_id;
											}


											$is_post = get_post();

											if (!in_array($is_post->ID, $thisposts)) {

												return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
											}
										} else {
											if (is_single($is_post->ID)) {
												return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
											}
										}
									}
								}
							}
							if ($inject_content_type == 'post') {
								foreach (explode(',', $specific_post) as $specific_post_id) {
									$is_post = get_post($specific_post_id);
									if ($is_post) {
										if (($inject_content_type == 'post' && ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate))) && is_single($is_post->ID)) {
											return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
										}
									}
								}
							}
							if ($inject_content_type == 'page') {
								foreach (explode(',', $specific_post) as $specific_post_id) {

									$is_page = get_post($specific_post_id);
									if ($is_page) {
										if (($inject_content_type == 'page' && (($startingdate == '' || $expirydate == '') || ($expirydate >= $currentdate && $startingdate <= $currentdate))) && is_page($is_page->ID)) {
											return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
										}
									}
								}
							}

							if ($inject_content_type == 'all_post' && ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate)) && is_single()) {


								if ($inject_content_type2 == 'post2') {
									$thisposts = array();
									foreach (explode(',', $specific_post2) as $specific_post_id) {
										$thisposts[] = $specific_post_id;
									}


									$is_post = get_post();

									if (!in_array($is_post->ID, $thisposts)) {

										return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
									}
								} else {
									return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
								}
							}

							if ($inject_content_type == 'all_page' && ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate)) && is_page()) {
								if ($inject_content_type2 == 'page2') {
									$thisposts = array();
									foreach (explode(',', $specific_post2) as $specific_post_id) {
										$thisposts[] = $specific_post_id;
									}


									$is_post = get_post();

									if (!in_array($is_post->ID, $thisposts)) {

										return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
									}
								} else {

									return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
								}
							}

							if ($inject_content_type == 'post_page' && ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate)) && (is_page() || is_single())) {
								if ($inject_content_type2 == 'page2') {
									$thisposts = array();
									foreach (explode(',', $specific_post2) as $specific_post_id) {
										$thisposts[] = $specific_post_id;
									}


									$is_post = get_post();

									if (!in_array($is_post->ID, $thisposts)) {

										return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
									}
								} else if ($inject_content_type2 == 'post2') {
									$thisposts = array();
									foreach (explode(',', $specific_post2) as $specific_post_id) {
										$thisposts[] = $specific_post_id;
									}


									$is_post = get_post();

									if (!in_array($is_post->ID, $thisposts)) {

										return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
									}
								} else {
									return $this->update_content($content, $tag, $num_of_blocks, $p, $after_before);
								}
							}


							return $content;
						},
						// array( $this, 'test' ),
						0
					);
					// do whatever you want with it
				}
			}
		}

		/**
		 * Load our block assets.
		 *
		 * @return void
		 */
		public function enqueue_editor_assets()
		{
			global $post_type;
			// if($this->post_type == $post_type){
			wp_enqueue_script(
				'put-me-anywhere-block-js',
				$this->plugin->asset_url('js/dist/editor.js'),
				array(
					'lodash',
					'react',
					'wp-block-editor',
				),
				$this->plugin->asset_version()
			);
			// }
		}

		public function register_post_type()
		{
			register_post_type(
				$this->post_type,
				array(
					'labels'              => array(
						'name'               => __('Block Injector', 'pmab'),
						'singular_name'      => __('Block Injector', 'pmab'),
						'add_new'            => __('Add New', 'pmab'),
						'add_new_item'       => __('Add New Block Injector', 'pmab'),
						'edit_item'          => __('Edit Block Injector', 'pmab'),
						'new_item'           => __('New Block Injector', 'pmab'),
						'all_items'          => __('Block Injector', 'pmab'),
						'view_item'          => __('View Block Injector', 'pmab'),
						'search_items'       => __('Search Block Injector', 'pmab'),
						'not_found'          => __('Nothing found', 'pmab'),
						'not_found_in_trash' => __('Nothing found in Trash', 'pmab'),
						'parent_item_colon'  => '',
					),

					'public'              => true,
					'show_ui'             => true,
					// 'show_in_menu'        => 'ct-dashboard',
					'show_in_menu'        => true,
					'publicly_queryable'  => false,
					'can_export'          => true,
					'query_var'           => true,
					'has_archive'         => false,
					'hierarchical'        => false,
					'show_in_rest'        => true,
					'exclude_from_search' => true,

					'supports'            => array(
						'title',
						'editor',
						// 'fw-page-builder'
						// 'thumbnail',
						// 'revisions'
					),

					'capabilities'        => array(
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
				)
			);
		}


		public function save_post($post_id)
		{
			if (isset($_POST['_pmab_meta_number_of_blocks'], $_POST['_pmab_meta_type'], $_POST['pmab_plugin_field']) && wp_verify_nonce($_POST['pmab_plugin_field'], 'pmab_plugin_nonce')) {
				update_post_meta($post_id, '_pmab_meta_number_of_blocks', sanitize_text_field($_POST['_pmab_meta_number_of_blocks']));
				update_post_meta($post_id, '_pmab_meta_specific_post', sanitize_text_field($_POST['_pmab_meta_specific_post']));
				update_post_meta($post_id, '_pmab_meta_specific_post2', sanitize_text_field($_POST['_pmab_meta_specific_post2']));
				update_post_meta($post_id, '_pmab_meta_tags', sanitize_text_field($_POST['_pmab_meta_tags']));
				update_post_meta($post_id, '_pmab_meta_category', $_POST['_pmab_meta_category']);
				update_post_meta($post_id, '_pmab_meta_type', sanitize_text_field($_POST['_pmab_meta_type']));
				update_post_meta($post_id, '_pmab_meta_type2', sanitize_text_field($_POST['_pmab_meta_type2']));
				update_post_meta($post_id, '_pmab_meta_tag_n_fix', sanitize_text_field(isset($_POST['_pmab_meta_tag_n_fix']) ? $_POST['_pmab_meta_tag_n_fix'] : 'p_after'));
				update_post_meta($post_id, '_pmab_meta_expiredate', $_POST['_pmab_meta_expiredate']);
				update_post_meta($post_id, '_pmab_meta_startdate', $_POST['_pmab_meta_startdate']);
			}
		}


		/**
		 * Render Meta Box content.
		 *
		 * @param WP_Post $post The post object.
		 */
		public function render_meta_box_content($post)
		{

			// Add an nonce field so we can check for it later.
			wp_nonce_field('pmab_plugin_nonce', 'pmab_plugin_field');

			// Use get_post_meta to retrieve an existing value from the database.
			$_pmab_meta_number_of_blocks = get_post_meta($post->ID, '_pmab_meta_number_of_blocks', true);
			$_pmab_meta_specific_post    = get_post_meta($post->ID, '_pmab_meta_specific_post', true);
			$_pmab_meta_specific_post2   = get_post_meta($post->ID, '_pmab_meta_specific_post2', true);
			$_pmab_meta_tags    		 = get_post_meta($post->ID, '_pmab_meta_tags', true);
			$_pmab_meta_type             = get_post_meta($post->ID, '_pmab_meta_type', true);
			$_pmab_meta_type2            = get_post_meta($post->ID, '_pmab_meta_type2', true);
			$_pmab_meta_tag_n_fix        = get_post_meta($post->ID, '_pmab_meta_tag_n_fix', true);
			$_pmab_meta_expiredate       = get_post_meta($post->ID, '_pmab_meta_expiredate', true);
			$_pmab_meta_startdate        = get_post_meta($post->ID, '_pmab_meta_startdate', true);
			$_pmab_meta_category         = get_post_meta($post->ID, '_pmab_meta_category', true);


			// Display the form, using the current value & Template .
 
			
			$this->viewTemplate(plugin_dir_path(__FILE__) . 'View.php',[
				'_pmab_meta_type'=>$_pmab_meta_type,
				'_pmab_meta_type2'=>$_pmab_meta_type2,
				'_pmab_meta_number_of_blocks'=>$_pmab_meta_number_of_blocks,
				'_pmab_meta_specific_post'=>$_pmab_meta_specific_post,
				'_pmab_meta_specific_post2'=>$_pmab_meta_specific_post2,
				'_pmab_meta_tags' => $_pmab_meta_tags,
				'_pmab_meta_tag_n_fix' => $_pmab_meta_tag_n_fix,
				'_pmab_meta_expiredate' => $_pmab_meta_expiredate,
				'_pmab_meta_startdate'  => $_pmab_meta_startdate,
				'_pmab_meta_category'  => $_pmab_meta_category				
				]
			);
		}


		public function viewTemplate($filePath, $variables = array(), $print = true)
		{
			
			// die(var_dump($variables));
			$output = NULL;
			if (file_exists($filePath)) {
				// Extract the variables to a local namespace
				extract($variables);

				// Start output buffering
				ob_start();

				// Include the template file
				include $filePath;

				// End buffering and return its contents
				$output = ob_get_clean();
			}
			if ($print) {
				echo $output;
			}
			return $output;
		}

		public function selected($val, $val2)
		{
			return $val === $val2 ? 'selected=selected' : '';
		}
		/**
		 * Adds the meta box container.
		 */
		public function add_meta_box($post_type)
		{
			// Limit meta box to certain post types.
			add_meta_box(
				'some_meta_box_name',
				__('Location and Position', 'pmab'),
				array($this, 'render_meta_box_content'),
				$this->post_type,
				'side',
				'high'
			);
		}


		public function update_content($content, $tag, $num_of_blocks, $p, $after_before)
		{
			// global $content, $tag, $num_of_blocks, $p,$after_before;
			$content_array = explode("</$tag>", $content);
			array_splice($content_array, $num_of_blocks, 0, array($p->post_content));
			$update_content = implode("</$tag>", $content_array);
			return $update_content;
		}
	}
}
