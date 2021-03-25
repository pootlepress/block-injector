<?php
/**
 * Router class.
 *
 * @package PutBlocksAnywhere
 */
if ( ! class_exists( 'PMAB_Router' ) ) {
	/**
	 * Plugin Router.
	 */
	class PMAB_Router {

		private $post_type = 'put_blocks_anywhere';

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
		public function __construct( $plugin ) {
			$this->plugin = $plugin;
			$this->manual_hook();
		}

		/**
		 * Hook into WP.
		 *
		 * @return void
		 */
		public function init() {
			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );
			// add_filter( 'the_content', array( $this, 'test' ), 1 );
		}

		public function manual_hook() {
			 // code...
			$posts = get_posts(
				array(
					'post_type'      => $this->post_type,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
				)
			);
			// var_dump( $posts );
			// die;
			// loop over each post
			foreach ( $posts as $p ) {

				$id = $p->ID;
				// get the meta you need form each post_pmab_meta_specific_post
				$num_of_blocks       = get_post_meta( $id, '_pmab_meta_number_of_blocks', true );
				$specific_post       = get_post_meta( $id, '_pmab_meta_specific_post', true );
				$tag_type            = get_post_meta( $id, '_pmab_meta_tag_n_fix', true );
				$inject_content_type = get_post_meta( $id, '_pmab_meta_type', true );
				$stdate              = get_post_meta( $id, '_pmab_meta_stdate', true );
				$exdate              = get_post_meta( $id, '_pmab_meta_exdate', true );

				// exit();
				$tag_type = explode( '_', $tag_type );
				if ( ! empty( $tag_type ) && isset( $tag_type[0] ) && isset( $tag_type[1] ) ) {

					$tag          = $tag_type[0];
					$after_before = $tag_type[1];

					add_filter(
						'the_content',
						function ( $content ) use ( $inject_content_type, $p, $tag, $num_of_blocks, $after_before, $exdate, $stdate, $specific_post ) {

							if ( $tag == 'top' || $num_of_blocks == '' ) {
								$num_of_blocks = 0;
							} elseif ( $tag == 'bottom' ) {
								$num_of_blocks = PHP_INT_MAX;
							}

							// Check if we're inside the main loop in a single Post.
							date_default_timezone_set( 'Asia/Karachi' );
							$date = date( 'Y-m-d\TH:i', time() ); // Date object using current date and time
							$dt   = $date;
							// echo $dt;
							// echo $exdate;

							$edate = $exdate;
							$sdate = $stdate;
							if ( $inject_content_type == 'post' ) {
								$mypost = get_page_by_title( $specific_post, '', 'post' );
								if ( $mypost ) {
									if ( ( $inject_content_type == 'post' && ( $edate >= $dt || $edate == '' ) && $sdate <= $dt ) && is_single( $mypost->ID ) ) {
										return $this->update_content( $content, $tag, $num_of_blocks, $p, $after_before );
									}
								}
							}
							if ( $inject_content_type == 'page' ) {
								$mypage = get_page_by_title( $specific_post, '', 'page' );
								if ( $mypage ) {
									if ( ( $inject_content_type == 'page' && $edate >= $dt && $sdate <= $dt ) && is_page( $mypage->ID ) ) {
										return $this->update_content( $content, $tag, $num_of_blocks, $p, $after_before );
									}
								}
							}
							if ( ( $inject_content_type == 'all_post' && $edate >= $dt && $sdate <= $dt ) && is_single() ) {
								return $this->update_content( $content, $tag, $num_of_blocks, $p, $after_before );
							}
							// if ( ( $inject_content_type == 'post' && $edate >= $dt && $sdate <= $dt ) && is_single(@$mypost->ID) ) {
							// return $this->update_content( $content, $tag, $num_of_blocks, $p, $after_before );
							// }
							if ( ( $inject_content_type == 'all_page' && $edate >= $dt && $sdate <= $dt ) && is_page() ) {
								return $this->update_content( $content, $tag, $num_of_blocks, $p, $after_before );
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
		public function enqueue_editor_assets() {
			global $post_type;
			// if($this->post_type == $post_type){
				wp_enqueue_script(
					'put-me-anywhere-block-js',
					$this->plugin->asset_url( 'js/dist/editor.js' ),
					array(
						'lodash',
						'react',
						'wp-block-editor',
					),
					$this->plugin->asset_version()
				);
			// }
		}

		public function register_post_type() {
			register_post_type(
				$this->post_type,
				array(
					'labels'              => array(
						'name'               => __( 'Put Blocks Anywheres', 'pmab' ),
						'singular_name'      => __( 'Put Blocks Anywhere', 'pmab' ),
						'add_new'            => __( 'Add New', 'pmab' ),
						'add_new_item'       => __( 'Add New Put Blocks Anywhere', 'pmab' ),
						'edit_item'          => __( 'Edit Put Blocks Anywhere', 'pmab' ),
						'new_item'           => __( 'New Put Blocks Anywhere', 'pmab' ),
						'all_items'          => __( 'Put Blocks Anywheres', 'pmab' ),
						'view_item'          => __( 'View Put Blocks Anywhere', 'pmab' ),
						'search_items'       => __( 'Search Put Blocks Anywheres', 'pmab' ),
						'not_found'          => __( 'Nothing found', 'pmab' ),
						'not_found_in_trash' => __( 'Nothing found in Trash', 'pmab' ),
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


		public function save_post( $post_id ) {
			if ( isset( $_POST['_pmab_meta_number_of_blocks'], $_POST['_pmab_meta_type'], $_POST['pmab_plugin_field'] ) && wp_verify_nonce( $_POST['pmab_plugin_field'], 'pmab_plugin_nonce' ) ) {
				update_post_meta( $post_id, '_pmab_meta_number_of_blocks', sanitize_text_field( $_POST['_pmab_meta_number_of_blocks'] ) );
				update_post_meta( $post_id, '_pmab_meta_specific_post', sanitize_text_field( $_POST['_pmab_meta_specific_post'] ) );

				update_post_meta( $post_id, '_pmab_meta_type', sanitize_text_field( $_POST['_pmab_meta_type'] ) );
				update_post_meta( $post_id, '_pmab_meta_tag_n_fix', sanitize_text_field( isset( $_POST['_pmab_meta_tag_n_fix'] ) ? $_POST['_pmab_meta_tag_n_fix'] : 'p_after' ) );
				update_post_meta( $post_id, '_pmab_meta_exdate', $_POST['_pmab_meta_exdate'] );
				update_post_meta( $post_id, '_pmab_meta_stdate', $_POST['_pmab_meta_stdate'] );
			}
		}


		/**
		 * Render Meta Box content.
		 *
		 * @param WP_Post $post The post object.
		 */
		public function render_meta_box_content( $post ) {

			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'pmab_plugin_nonce', 'pmab_plugin_field' );

			// Use get_post_meta to retrieve an existing value from the database.
			$_pmab_meta_number_of_blocks = get_post_meta( $post->ID, '_pmab_meta_number_of_blocks', true );
			$_pmab_meta_specific_post    = get_post_meta( $post->ID, '_pmab_meta_specific_post', true );
			$_pmab_meta_type             = get_post_meta( $post->ID, '_pmab_meta_type', true );
			$_pmab_meta_tag_n_fix        = get_post_meta( $post->ID, '_pmab_meta_tag_n_fix', true );
			$_pmab_meta_exdate           = get_post_meta( $post->ID, '_pmab_meta_exdate', true );
			$_pmab_meta_stdate           = get_post_meta( $post->ID, '_pmab_meta_stdate', true );

			// Display the form, using the current value.
			?>
			<div>
			<p><label for="_pmab_meta_number_of_blocks">
				<?php _e( 'After Certain Number', 'pmab' ); ?>
			</label>
			<input type="number" id="_pmab_meta_number_of_blocks" name="_pmab_meta_number_of_blocks" value="<?php echo esc_attr( $_pmab_meta_number_of_blocks ); ?>" size="25" class="postbox" /></p>
			<p>
			<label for="_pmab_meta_tag_n_fix"><?php _e( 'Select post type', 'pmab' ); ?></label>
			<select name="_pmab_meta_tag_n_fix" id="_pmab_meta_tag_n_fix" class="postbox col-12">
				<option value="" disabled style="font-weight: bolder;">Location</option>
				<option value="top_before" <?php echo $this->selected( $_pmab_meta_tag_n_fix, 'top_before' ); ?>>Top</option>
				<option value="bottom_after" <?php echo $this->selected( $_pmab_meta_tag_n_fix, 'bottom_after' ); ?>>Bottom</option>
				<option value="h2_after" <?php echo $this->selected( $_pmab_meta_tag_n_fix, 'h2_after' ); ?>>After Heading</option>
				<option value="p_after" <?php echo $this->selected( $_pmab_meta_tag_n_fix, 'p_after' ); ?>>After Blocks</option>
			</select>
			</p>
			
			<p>
			<!-- <label for="_pmab_meta_type"><php _e( 'Select post type', 'pmab' ); ?></label> -->
			<!-- <select name="_pmab_meta_type" id="_pmab_meta_type" class="postbox">
				<option value="post_page">Entire Website</option>
				<option value="post" <php echo $this->selected( $_pmab_meta_type, 'post' ); ?>>Post</option>
				<option value="page" <php echo $this->selected( $_pmab_meta_type, 'page' ); ?>>Page</option>
			</select> -->
			<label for="_pmab_meta_type"><?php _e( 'Posts', 'pmab' ); ?></label>
			<select name="_pmab_meta_type" id="_pmab_meta_type" class="postbox">
			<option  disabled style="font-weight: bolder;">Post</option>
				<option value="all_post" <?php echo $this->selected( $_pmab_meta_type, 'all_post' ); ?>>All Posts</option>
				<option value="post" <?php echo $this->selected( $_pmab_meta_type, 'post' ); ?>>Specific Posts</option>
				<option  disabled style="font-weight: bolder;"> Pages</option>
				<option value="all_page" <?php echo $this->selected( $_pmab_meta_type, 'all_page' ); ?>>All Pages</option>
				<option value="page" <?php echo $this->selected( $_pmab_meta_type, 'page' ); ?>>Specific Page</option>
			</select>
		
			</p>
			<p class="specificpost" style="<?php echo $_pmab_meta_specific_post == '' ? 'display: none;' : ''; ?>">
			<label for="_pmab_meta_specific_post"><?php _e( 'Name', 'pmab' ); ?></label>
			<input type="text" id="_pmab_meta_specific_post" name="_pmab_meta_specific_post" value="<?php echo esc_attr( $_pmab_meta_specific_post ); ?>" size="25" class="postbox" /></p>

			</p>
			<p>
			<label for="_pmab_meta_stdate"><?php _e( 'Select Start Date', 'pmab' ); ?></label>
			<input type="datetime-local" id="_pmab_meta_stdate" name="_pmab_meta_stdate" value="<?php echo esc_attr( $_pmab_meta_stdate ); ?>" size="25" class="postbox" /></p>


			<p>
			<label for="_pmab_meta_exdate"><?php _e( 'Select Expiry Date', 'pmab' ); ?></label>
			<input type="datetime-local" id="_pmab_meta_exdate" name="_pmab_meta_exdate" value="<?php echo esc_attr( $_pmab_meta_exdate ); ?>" size="25" class="postbox" /></p>
			
			<div  style="justify-content: space-evenly; display: flex;">
			<button id="pmabDesktopsize"><span class="dashicons dashicons-desktop"></span></button>
			<button id="pmabTabletsize"><span class="dashicons dashicons-tablet"></span></button>
			<button id="pmabMobilesize"><span class="dashicons dashicons-smartphone"></span></button></div>
			
			</div>
			
			<?php
		}

		public function selected( $val, $val2 ) {
			return $val === $val2 ? 'selected=selected' : '';
		}
		/**
		 * Adds the meta box container.
		 */
		public function add_meta_box( $post_type ) {
			// Limit meta box to certain post types.
			add_meta_box(
				'some_meta_box_name',
				__( 'Location & Priority', 'pmab' ),
				array( $this, 'render_meta_box_content' ),
				$this->post_type,
				'side',
				'high'
			);
		}

		public function update_content( $content, $tag, $num_of_blocks, $p, $after_before ) {
			// global $content, $tag, $num_of_blocks, $p,$after_before;
			$content_array = explode( "</$tag>", $content );
			array_splice( $content_array, $num_of_blocks, 0, array( $p->post_content ) );
			$update_content = implode( "</$tag>", $content_array );
			return $update_content;
		}
	}


}
