<?php

/**
 * Router class.
 * @package BlockInjector
 */

include 'class-admin-save-post.php';

if ( ! class_exists( 'PMAB_Admin' ) ) {
	/**
	 * Plugin Router.
	 */
	class PMAB_Admin extends PMAB_Admin_Save_Post {
		private $post_type = 'block_injector';

		/**
		 * Plugin interface.
		 * @var PMAB_Plugin
		 */
		protected $plugin;

		/**
		 * Content interface.
		 * @var PMAB_Content
		 */
		protected $content;

		/**
		 * Setup the plugin instance.
		 *
		 * @param PMAB_Plugin $plugin Instance of the plugin abstraction.
		 */
		public function __construct( PMAB_Plugin $plugin, PMAB_Content $content ) {
			$this->plugin  = $plugin;
			$this->content = $content;
		}

		/**
		 * Hook into WP.
		 * @return void
		 */
		public function init(): void {
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_post' ) );
		}

		/**
		 * Load js assets on editor.
		 * @return void
		 */
		public function enqueue_editor_assets(): void {
			wp_enqueue_script(
				'put-me-anywhere-block-js',
				$this->plugin->asset_url( 'js/src/editor.js' ),
				array( 'lodash', 'react', 'wp-block-editor', ),
				$this->plugin->asset_version()
			);

			wp_enqueue_script(
				'select2',
				"//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"
			);

			wp_enqueue_style(
				'select2',
				"//cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css"
			);
		}

		/**
		 * Register meta box container.
		 * @return void
		 */
		public function register_post_type(): void {
			register_post_type(
				$this->post_type,
				array(
					'labels' => array(
						'name'               => __( 'Block Injector', 'pmab' ),
						'singular_name'      => __( 'Block Injector', 'pmab' ),
						'add_new'            => __( 'Add New', 'pmab' ),
						'add_new_item'       => __( 'Add New Block Injector', 'pmab' ),
						'edit_item'          => __( 'Edit Block Injector', 'pmab' ),
						'new_item'           => __( 'New Block Injector', 'pmab' ),
						'all_items'          => __( 'Block Injector', 'pmab' ),
						'view_item'          => __( 'View Block Injector', 'pmab' ),
						'search_items'       => __( 'Search Block Injector', 'pmab' ),
						'not_found'          => __( 'Nothing found', 'pmab' ),
						'not_found_in_trash' => __( 'Nothing found in Trash', 'pmab' ),
						'parent_item_colon'  => '',
					),

					'public'              => true,
					'show_ui'             => true,
					'show_in_menu'        => true,
					'publicly_queryable'  => false,
					'can_export'          => true,
					'query_var'           => true,
					'has_archive'         => false,
					'hierarchical'        => false,
					'show_in_rest'        => true,
					'exclude_from_search' => true,

					'supports' => array(
						'title',
						'editor',
					),

					'capabilities' => array(
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

			register_taxonomy(
				'block_injector_location',
				[ $this->post_type ],
				[
					'hierarchical'      => false,
					'show_ui'           => false,
					'show_in_nav_menus' => false,
					'query_var'         => is_admin(),
					'rewrite'           => false,
					'public'            => false,
					'label'             => __( 'Block injector location', 'pmab' ),
				]
			);
		}

		/**
		 * Adds the meta box container.
		 *
		 * @return void
		 */
		public function add_meta_box() {
			// Limit meta box to certain post types.
			add_meta_box(
				'pmab_block_injector_meta',
				__( 'Location and Position', 'pmab' ),
				array( $this, 'render_meta_box_content' ),
				$this->post_type,
				'side',
				'high'
			);
		}

		/**
		 * Render Meta Box content.
		 *
		 * @param WP_Post $post The post object.
		 */
		public function render_meta_box_content( WP_Post $post ): void {
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'pmab_plugin_nonce', 'pmab_plugin_field' );

			// Use get_post_meta to retrieve an existing value from the database.
			$_pmab_meta_number_of_blocks      = get_post_meta( $post->ID, '_pmab_meta_number_of_blocks', true );
			$_pmab_meta_specific_post         = get_post_meta( $post->ID, '_pmab_meta_specific_post', true );
			$_pmab_meta_specific_woocategory  = get_post_meta( $post->ID, '_pmab_meta_specific_woocategory', true );
			$_pmab_meta_specific_post_exclude = get_post_meta( $post->ID, '_pmab_meta_specific_post_exclude', true );
			$_pmab_meta_tags                  = get_post_meta( $post->ID, '_pmab_meta_tags', true );
			$_pmab_meta_type                  = get_post_meta( $post->ID, '_pmab_meta_type', true );
			$_pmab_meta_type2                 = get_post_meta( $post->ID, '_pmab_meta_type2', true );
			$_pmab_meta_tag_n_fix             = get_post_meta( $post->ID, '_pmab_meta_tag_n_fix', true );
			$_pmab_meta_hook                  = get_post_meta( $post->ID, '_pmab_meta_hook', true );
			$_pmab_meta_expiredate            = get_post_meta( $post->ID, '_pmab_meta_expiredate', true );
			$_pmab_meta_startdate             = get_post_meta( $post->ID, '_pmab_meta_startdate', true );
			$_pmab_meta_category              = get_post_meta( $post->ID, '_pmab_meta_category', true );
			$_pmab_meta_woo_category          = get_post_meta( $post->ID, '_pmab_meta_woo_category', true );

			$args          = array(
				"hide_empty" => 0,
				'orderby'    => 'name',
				'exclude'    => '',
				'include'    => '',
				'parent'     => 0
			);
			$categories    = get_categories( $args );
			$wooargs       = array(
				'taxonomy'   => 'product_cat',
				"hide_empty" => 0,
				'orderby'    => 'name',
				'exclude'    => '',
				'include'    => '',
				'parent'     => 0
			);
			$woocategories = get_categories( $wooargs );
			// Display the form, using the current value & Template .

			$this->view_template(
				plugin_dir_path( __FILE__ ) . 'tpl-meta-box.php',
				array(
					'_pmab_meta_type'                  => $_pmab_meta_type,
					'_pmab_meta_type2'                 => $_pmab_meta_type2,
					'_pmab_meta_number_of_blocks'      => $_pmab_meta_number_of_blocks,
					'_pmab_meta_specific_post'         => $_pmab_meta_specific_post,
					'_pmab_meta_specific_woocategory'  => $_pmab_meta_specific_woocategory,
					'_pmab_meta_specific_post_exclude' => $_pmab_meta_specific_post_exclude,
					'_pmab_meta_tags'                  => $_pmab_meta_tags,
					'_pmab_meta_tag_n_fix'             => $_pmab_meta_tag_n_fix,
					'_pmab_meta_hook'                  => $_pmab_meta_hook,
					'_pmab_meta_expiredate'            => $_pmab_meta_expiredate,
					'_pmab_meta_startdate'             => $_pmab_meta_startdate,
					'_pmab_meta_category'              => $_pmab_meta_category,
					'_pmab_meta_woo_category'          => $_pmab_meta_woo_category,
					'_pmab_categories'                 => $categories,
					'_pmab_woo_categories'             => $woocategories,
				)
			);
		}

		/*
		 * Meta Preview Template.
		 *
		 * @param:string $file_path Real file path.
		 * @param:array $variables Pass data into variable.
		 * @param:boolean $print.
		 * @return mixed
		 */
		public function view_template( $file_path, $variables = array(), $print = true ) {
			$output = null;
			if ( file_exists( $file_path ) ) {
				// Extract the variables to a local namespace.
				extract( $variables );

				// Start output buffering.
				ob_start();

				// Include the template file.
				include $file_path;

				// End buffering and return its contents
				$output = ob_get_clean();
			}
			if ( $print ) {
				echo $output;
			}

			return $output;
		}
	}
}
