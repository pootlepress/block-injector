<?php

/**
 * Router class.
 * @package BlockInjector
 */

include 'class-admin-post.php';

if ( ! class_exists( 'PMAB_Admin' ) ) {
	/**
	 * Plugin Router.
	 */
	class PMAB_Admin extends PMAB_Admin_Post {
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
			add_action( "manage_{$this->post_type}_posts_columns", array( $this, 'posts_columns' ) );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'posts_columns_filter' ), 10, 2 );
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
			register_post_type( $this->post_type, $this->post_type_args() );

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

		public function posts_columns( $columns ) {


			return
				array_slice( $columns, 0, 2, true ) +
				[
					'_pmab_meta_type'      => 'Location',
					'_pmab_meta_tag_n_fix' => 'Position',
				] + array_slice( $columns, 2, null, true );
		}

		public function posts_columns_filter( $column, $post ) {
			if ( in_array( $column, [ '_pmab_meta_type', '_pmab_meta_tag_n_fix' ] ) ) {
				$labels = [
					'_pmab_meta_type'      => [
						'post_page'              => 'Entire Website',
						'all_post'               => 'All Posts\n\t\t\t',
						'post'                   => 'Specific Posts',
						'category'               => 'Posts By Category',
						'tags'                   => 'Posts By Tag',
						'all_page'               => 'All Pages',
						'page'                   => 'Specific Page',
						'woo_all_pages'          => 'All WooCommerce Pages',
						'woo_all_products'       => 'All Products',
						'woo_pro_category'       => 'Products by Category',
						'woo_tags'               => 'Products by Tag',
						'woo_product'            => 'Specific Product',
						'woo_all_category_pages' => 'All Category Pages',
						'woo_category_page'      => 'Specific Category Page',
						'woo_shop'               => 'Shop Page',
						'woo_account'            => 'My Account Page',
						'woo_basket'             => 'Basket Page',
						'woo_checkout'           => 'Checkout Page',
					],
					'_pmab_meta_tag_n_fix' => [
						'top_before'   => 'Top',
						'bottom_after' => 'Bottom',
						'h2_after'     => 'After Heading',
						'p_after'      => 'After Blocks',
					]
				];
				$meta = get_post_meta( $post, $column, 'single' );

				if ( ! empty( $labels[$column][$meta] ) ) {
					echo $labels[ $column ][ $meta ];
				}
			}
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
