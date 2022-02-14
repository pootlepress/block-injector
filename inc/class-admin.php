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
		public function init() {
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
			add_action( 'init', array( $this, 'register_post_type' ) );
			add_action( 'current_screen', array( $this, 'block_injector_screen' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( "manage_{$this->post_type}_posts_columns", array( $this, 'posts_columns' ) );
			add_action( "manage_{$this->post_type}_posts_custom_column", array( $this, 'posts_columns_filter' ), 10, 2 );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
			add_action( 'wp_ajax_pmab_posts', array( $this, 'admin_ajax_pmab_posts' ) );
		}

		public function block_injector_screen() {
			global $wpdb;
			$screen = get_current_screen();
			if ( 'edit-block_injector' === $screen->id ) {
				if ( ! empty( $_GET['pmab_toggle_status'] ) ) {
					if ( ! empty( $_GET['pmab_toggle_status'] ) || wp_verify_nonce( $_GET['pmab_toggle_status'], 'pmab_status_switch' ) ) {
						$new_status = 'publish';
						if ( ! empty( $_GET['pmab_from_status'] ) && 'publish' === $_GET['pmab_from_status'] ) {
							$new_status = 'draft';
						}
						$wpdb->update( $wpdb->posts, array( 'post_status' => $new_status ), array( 'ID' => $_GET['pmab_toggle_status'] ) );
					}
				}
				?>
				<style>
					.pmab-status-dot {
						margin: 0 .25em 0 0;
						height: 8px;
						width: 8px;
						display: inline-block;
						border-radius: 50%;
						background: #999;
					}

					.wp-core-ui a.button.button-small {
						line-height: 20px;
						min-height: 0;
						vertical-align: middle;
					}

					.wp-core-ui a.button.button-red {
						color: #a00;
						border-color: #a00;
					}
				</style>
				<?php
			}
		}

		public function admin_ajax_pmab_posts() {
			$resp = [
				'post'    => [],
				'page'    => [],
				'product' => [],
			];

			global $wpdb;

			$posts = $wpdb->get_results(
				"SELECT ID, post_title as title, post_type as type FROM {$wpdb->prefix}posts " .
				"WHERE post_type IN ( 'post', 'page', 'product' ) ORDER BY title ASC LIMIT 999" );

			foreach ( $posts as $p ) {
				$resp[ $p->type ][] = [ $p->ID, $p->title ];
			}
			header( 'Content-type: application/json' );
			die( json_encode( $resp ) );
		}

		/**
		 * Load js assets on editor.
		 * @return void
		 */
		public function enqueue_editor_assets() {

			if ( get_post_type() !== $this->post_type ) return;
			wp_enqueue_script(
				'put-me-anywhere-block-js',
				$this->plugin->asset_url( 'js/src/editor.js' ),
				array( 'lodash', 'react', 'wp-block-editor', ),
				$this->plugin->asset_version()
			);

			wp_localize_script(
				'put-me-anywhere-block-js',
				'pmabProps',
				[
					'adminAjax' => admin_url( '/admin-ajax.php' ),
				]
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
		public function register_post_type() {
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

		public function admin_menu() {
			add_submenu_page(
				"edit.php?post_type=$this->post_type",
				__( 'How to use Block injector', 'pmab' ),
				__( 'Introduction', 'pmab' ),
				'manage_options',
				'block-injector-intro',
				[ $this, 'admin_page_tpl' ],
				10
			);
		}

		public function admin_page_tpl() {
			require 'tpl-admin-page.php';
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
		public function render_meta_box_content( WP_Post $post ) {
			// Add an nonce field so we can check for it later.
			wp_nonce_field( 'pmab_plugin_nonce', 'pmab_plugin_field' );

			// Use get_post_meta to retrieve an existing value from the database.
			$_pmab_meta_number_of_blocks      = get_post_meta( $post->ID, '_pmab_meta_number_of_blocks', true );
			$_pmab_meta_specific_post         = get_post_meta( $post->ID, '_pmab_meta_specific_post', true );
			$_pmab_meta_specific_woocategory  = get_post_meta( $post->ID, '_pmab_meta_specific_woocategory', true );
			$_pmab_meta_specific_post_exclude = get_post_meta( $post->ID, '_pmab_meta_specific_post_exclude', true );
			$_pmab_meta_tags                  = get_post_meta( $post->ID, '_pmab_meta_tags', true );
			$_pmab_meta_type                  = get_post_meta( $post->ID, '_pmab_meta_type', true );
			$_pmab_meta_tag_n_fix             = get_post_meta( $post->ID, '_pmab_meta_tag_n_fix', true );
			$_pmab_meta_priority              = get_post_meta( $post->ID, '_pmab_meta_priority', true );
			$_pmab_meta_hook                  = get_post_meta( $post->ID, '_pmab_meta_hook', true );
			$_pmab_meta_expiredate            = get_post_meta( $post->ID, '_pmab_meta_expiredate', true );
			$_pmab_meta_on_days               = get_post_meta( $post->ID, '_pmab_meta_on_days', true );
			$_pmab_meta_from_time             = get_post_meta( $post->ID, '_pmab_meta_from_time', true );
			$_pmab_meta_to_time               = get_post_meta( $post->ID, '_pmab_meta_to_time', true );
			$_pmab_responsive_visibility      = get_post_meta( $post->ID, '_pmab_responsive_visibility', true );
			$_pmab_meta_startdate             = get_post_meta( $post->ID, '_pmab_meta_startdate', true );
			$_pmab_meta_category              = get_post_meta( $post->ID, '_pmab_meta_category', true );
			$_pmab_meta_woo_category          = get_post_meta( $post->ID, '_pmab_meta_woo_category', true );

			$args          = array(
				"hide_empty" => 0,
				'orderby'    => 'name',
				'exclude'    => '',
				'include'    => '',
//				'parent'     => 0
			);
			$categories    = get_categories( $args );
			$wooargs       = array(
				'taxonomy'   => 'product_cat',
				"hide_empty" => 0,
				'orderby'    => 'name',
				'exclude'    => '',
				'include'    => '',
//				'parent'     => 0
			);
			$woocategories = get_categories( $wooargs );
			// Display the form, using the current value & Template .

			if ( ! $_pmab_meta_priority ) {
				$_pmab_meta_priority = '0';
			}

			$this->view_template(
				plugin_dir_path( __FILE__ ) . 'tpl-meta-box.php',
				array(
					'_pmab_meta_type'                  => $_pmab_meta_type,
					'_pmab_meta_number_of_blocks'      => $_pmab_meta_number_of_blocks,
					'_pmab_meta_specific_post'         => $_pmab_meta_specific_post,
					'_pmab_meta_specific_woocategory'  => $_pmab_meta_specific_woocategory,
					'_pmab_meta_specific_post_exclude' => $_pmab_meta_specific_post_exclude,
					'_pmab_meta_tags'                  => $_pmab_meta_tags,
					'_pmab_meta_tag_n_fix'             => $_pmab_meta_tag_n_fix,
					'_pmab_meta_priority'              => $_pmab_meta_priority,
					'_pmab_meta_hook'                  => $_pmab_meta_hook,
					'_pmab_meta_expiredate'            => $_pmab_meta_expiredate,
					'_pmab_meta_startdate'             => $_pmab_meta_startdate,
					'_pmab_meta_on_days'               => $_pmab_meta_on_days,
					'_pmab_meta_from_time'             => $_pmab_meta_from_time,
					'_pmab_meta_to_time'               => $_pmab_meta_to_time,
					'_pmab_responsive_visibility'      => $_pmab_responsive_visibility,
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
					'_pmab_status'         => 'Status',
				] + array_slice( $columns, 2, null, true );
		}

		private function posts_columns_filter_metadata_terms( $meta ) {

			$meta = is_string( $meta ) ? explode( ',', str_replace( ' ', '', $meta ) ) : $meta;

			$terms = get_terms( [
				'taxonomy'   => [ 'product_cat', 'product_tag', 'category', 'post_tag' ],
				'include'    => $meta,
				'hide_empty' => false,
			] );

			$meta = wp_list_pluck( $terms, 'name' );

			return implode( ', ', $meta );
		}

		private function posts_columns_filter_metadata_posts( $meta ) {
			$meta = is_string( $meta ) ? explode( ',', str_replace( ' ', '', $meta ) ) : $meta;

			$posts = get_posts( [
				'post_type'   => 'any',
				'post_status' => 'any',
				'post__in'    => $meta
			] );

			$meta = wp_list_pluck( $posts, 'post_title' );

			return implode( ', ', $meta );
		}

		public function posts_columns_filter_metadata( $column, $post, $meta ) {
			$additional_metadata = [
				'_pmab_meta_type' => [
					'post'              => [ '_pmab_meta_specific_post', 'posts' ],
					'category'          => [ '_pmab_meta_category', 'terms' ],
					'tags'              => [ '_pmab_meta_tags', 'terms' ],
					'page'              => [ '_pmab_meta_specific_post', 'posts' ],
					'woo_pro_category'  => [ '_pmab_meta_woo_category', 'terms' ],
					'woo_pro_tags'      => [ '_pmab_meta_tags', 'terms' ],
					'woo_product'       => [ '_pmab_meta_specific_post', 'posts' ],
					'woo_category_page' => [ '_pmab_meta_specific_woocategory', 'terms' ],
				]
			];

			if ( ! empty( $additional_metadata[ $column ][ $meta ] ) ) {
				$metadata_to_fetch = $additional_metadata[ $column ][ $meta ];
				$meta              = get_post_meta( $post, $metadata_to_fetch[0], 'single' );

				$callback = "posts_columns_filter_metadata_{$metadata_to_fetch[1]}";
				if ( method_exists( $this, $callback ) ) {
					$meta = $this->$callback( $meta );
				}

				return ' (<small>' . $meta . '</small>)';
			}
		}

		public function posts_columns_filter( $column, $post_id ) {
			if ( in_array( $column, [ '_pmab_meta_type', '_pmab_meta_tag_n_fix' ] ) ) {
				$labels = [
					'_pmab_meta_type'      => [
						'post_page'                     => 'Entire Website',
						'all_post'                      => 'All Posts',
						'post'                          => 'Specific Posts',
						'category'                      => 'Posts By Category',
						'tags'                          => 'Posts By Tag',
						'all_page'                      => 'All Pages',
						'page'                          => 'Specific Page',
						'woo_all_pages'                 => 'All WooCommerce Pages',
						'woo_all_products'              => 'All Products',
						'woo_all_products_in_stock'     => 'Products in stock',
						'woo_all_products_out_of_stock' => 'Products out of stock',
						'woo_all_products_on_backorder' => 'Products on backorder',
						'woo_all_products_on_sale'      => 'Products on sale',
						'woo_pro_category'              => 'Products by Category',
						'woo_pro_tags'                  => 'Products by Tag',
						'woo_product'                   => 'Specific Product',
						'woo_all_category_pages'        => 'All Category Pages',
						'woo_category_page'             => 'Specific Category Page',
						'woo_shop'                      => 'Shop Page',
						'woo_account'                   => 'My Account Page',
						'woo_basket'                    => 'Basket Page',
						'woo_checkout'                  => 'Checkout Page',
					],
					'_pmab_meta_tag_n_fix' => [
						'above_header' => 'Above Header',
						'top_before'   => 'Top',
						'bottom_after' => 'Bottom',
						'h2_after'     => 'After Heading',
						'p_after'      => 'After Blocks',
					]
				];

				$meta = get_post_meta( $post_id, $column, 'single' );

				if ( ! empty( $labels[ $column ][ $meta ] ) ) {
					echo $labels[ $column ][ $meta ] . ' ' . $this->posts_columns_filter_metadata( $column, $post_id, $meta );
				}
			} else if ( '_pmab_status' === $column ) {
				$post = get_post( $post_id );

				$status_labels        = [
					'publish' => '<span class="pmab-status-dot" style="background: #1c0"></span> Active',
					'draft'   => '<span class="pmab-status-dot"></span> Inactive',
				];
				$switch_link          = add_query_arg( [
					'pmab_nonce'         => wp_create_nonce( 'pmab_status_switch' ),
					'pmab_toggle_status' => $post_id,
					'pmab_from_status'   => $post->post_status,
				] );
				$status_change_button = [
					'publish' => "<a href='$switch_link' class='button button-small button-red'>Disable</a>",
					'draft'   => "<a href='$switch_link' class='button button-small'>Enable</a>",
				];
				echo $status_labels[ $post->post_status ] . ' ';
				echo $status_change_button[ $post->post_status ];
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
