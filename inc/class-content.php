<?php

/**
 * Content class.
 *
 * @package BlockInjector
 */

if ( ! class_exists( 'class-content' ) ) {
	class PMAB_Content extends PMAB_Content_Filter {

		private $pmab_metas = array();

		/**
		 * Update props under after any changes to location taxonomy terms
		 * List of post types and taxonomies we support, others are included in any.
		 * @uses PMAB_Admin_Save_Post::$location_taxonomy_maps
		 * @var string[]
		 */
		private $known_query_objects = [
			'post',
			'page',
			'product',
			'product_cat',
		];

		public function __construct() {
			add_action( 'template_redirect', [ $this, 'extract_block_injectors_with_metas' ] );
			add_action( 'template_redirect', [ $this, 'push_to_specific_content' ], 11 );
			add_action( 'wp_print_footer_scripts', [ $this, 'footer_scripts' ], 11 );
		}

		public function footer_scripts() {
			?>
			<script>
				!function () {
					var jsBlocks = document.querySelectorAll( '.block_inject_div_js' );

					jsBlocks.forEach( el => el.parentNode.removeChild( el ) );

					window.addEventListener( 'load', ( event ) => {
						for ( let i = jsBlocks.length - 1; i >= 0; i -- ) {
							const target = jsBlocks[i];

							console.log( target );

							var tag_selector = target.dataset.tag_selector;
							if ( !tag_selector ) {
								tag_selector = target.dataset.tag === "h2" ?
									".page-description h1,.page-description  h2,.page-description h3,.page-description h4,.page-description h5,.page-description h6" :
									".page-description p,.site-main p";
							}
							var inserted = false;
							var matchedBlocks = document.querySelectorAll( tag_selector );
							matchedBlocks[0].parentNode.insertBefore( target, matchedBlocks[0] );
							var position = target.dataset.number_of_blocks;
							matchedBlocks.forEach( ( v, k ) => {
								if ( position === k + 1 || k + 1 === matchedBlocks.length && position > 9999 ) {
									v.after( target );
									inserted = true;
								}
							} );
						}
					} );
				}();
			</script>

			<?php
		}

		private function _post_id_in_list( $post_id, $included ) {
			if ( is_string( $included ) ) {
				$included = explode( ',', str_replace( ' ', '', $included ) );
			}

			return in_array( $post_id, $included );
		}

		private function get_block_injectors() {
//			wp_get_object_terms( $post->ID, 'block_injector_location' )

			$queried_object = get_queried_object();

//			echo '<pre>' . print_r( $queried_object, 1 ) . '</pre>';

			$location_terms = [ 'any' ];

			$query_args = array(
				'post_type'      => 'block_injector',
				'post_status'    => 'publish',
				'posts_per_page' => - 1,
				'order'          => 'ASC',
				'orderby'        => 'meta_value_num',
				'meta_key'       => '_pmab_meta_priority',
			);

			if ( $queried_object ) {
				$queried_object_type = get_class( $queried_object );

				if ( 'WP_Post' === $queried_object_type && in_array( $queried_object->post_type, $this->known_query_objects ) ) {
					$location_terms = [ $queried_object->post_type ];
				} else if ( 'WP_Post_Type' === $queried_object_type && $queried_object->name === 'product' ) {
					$location_terms = [ 'shop' ];
				} else if ( 'WP_Term' === $queried_object_type && in_array( $queried_object->taxonomy, $this->known_query_objects ) ) {
					$location_terms = [ $queried_object->taxonomy ];
				}
			}

			if ( ! empty( $location_terms ) ) {
				$query_args['tax_query'] = array(
					array(
						'taxonomy' => 'block_injector_location',
						'field'    => 'slug',
						'terms'    => $location_terms,
					),
				);
			}

			$block_injectors = get_posts( $query_args );

			return $block_injectors;
		}

		public function extract_block_injectors_with_metas() {
			foreach ( $this->get_block_injectors() as $p ) {

				$specific_post_exclude = get_post_meta( $p->ID, '_pmab_meta_specific_post_exclude', true );
				$tag_type              = get_post_meta( $p->ID, '_pmab_meta_tag_n_fix', true );
				$pmab_meta             = array(
					'p'                     => $p,
					'num_of_blocks'         => get_post_meta( $p->ID, '_pmab_meta_number_of_blocks', true ),
					'specific_post_exclude' => $specific_post_exclude,
					'thisposts_exclude'     => is_string( $specific_post_exclude ) ? explode( ',', $specific_post_exclude ) : array(),
					'priority'              => get_post_meta( $p->ID, '_pmab_meta_priority', true ),
					'tag_type'              => $tag_type,
					'startdate'             => get_post_meta( $p->ID, '_pmab_meta_startdate', true ),
					'expiredate'            => get_post_meta( $p->ID, '_pmab_meta_expiredate', true ),
					'inject_content_type'   => get_post_meta( $p->ID, '_pmab_meta_type', true ),
					'specific_woocategory'  => get_post_meta( $p->ID, '_pmab_meta_specific_woocategory', true ),
					'category'              => get_post_meta( $p->ID, '_pmab_meta_category', true ),
					'woo_category'          => get_post_meta( $p->ID, '_pmab_meta_woo_category', true ),
					'tags'                  => get_post_meta( $p->ID, '_pmab_meta_tags', true ),
//					'woo_hooks'             => get_post_meta( $p->ID, '_pmab_meta_hook', true ),
				);

				$specific_post              = get_post_meta( $p->ID, '_pmab_meta_specific_post', true );
				$pmab_meta['specific_post'] = is_string( $specific_post ) ? explode( ',', $specific_post ) : array();
				$pmab_meta['dateandtime']   = pmab_expire_checker( $pmab_meta['startdate'], $pmab_meta['expiredate'] );

				$num_of_blocks = get_post_meta( $p->ID, '_pmab_meta_number_of_blocks', true );

				$tag_map = [
					'top_before'   => 'top',
					'bottom_after' => 'bottom',
					'h2_after'     => 'h2',
					'p_after'      => 'p',
				];

				$tag = isset( $tag_map[ $tag_type ] ) ? $tag_map[ $tag_type ] : $tag_type;

				if ( $tag ) {
					$pmab_meta['tag'] = $tag;
					switch ( $tag ) {
						case 'top':
						case 'woo':
							$num_of_blocks = 0;
							break;
						case 'bottom':
							$num_of_blocks = PHP_INT_MAX;
							break;
						case 'h2':
							$num_of_blocks --;
							break;
					}
				}
				$pmab_meta['num_of_blocks'] = $num_of_blocks;

				array_push( $this->pmab_metas, $pmab_meta );
			}
		}

		private static function block_inject_div_with_script( $tag, $num_of_blocks, $p, $tag_selector = '' ) {
			$num_of_blocks = $tag == "p" ? $num_of_blocks : $num_of_blocks + 1;
			?>
			<div
				id='block_inject_div-<?php echo $p->ID ?>' class='block_inject_div_js'
				data-tag='<?php echo $tag ?>'
				data-tag_selector='<?php echo $tag_selector ?>'
				data-number_of_blocks='<?php echo $num_of_blocks ?>'>
				<?php PMAB_Content::output_injection( $p ); ?>
			</div>
			<?php
		}

		private function push_content_post_page( $pmab_meta ) {

			if ( is_home() || is_category() || is_tag() ) {
				extract( $pmab_meta );
				add_action(
					'wp_footer',
					static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
						$selector = '';
						if ( 'h2' !== $tag ) {
							$selector = ".post.status-publish";
						}
						PMAB_Content::block_inject_div_with_script( $tag, $num_of_blocks, $p, $selector );
					},
					0
				);
			}
			$this->push_content_woo_all_pages( $pmab_meta );
		}

		private function push_content_woo_all_pages( $pmab_meta ) {
			$this->push_content_woo_shop( $pmab_meta );
			$this->push_content_woo_category_page( $pmab_meta );
			$this->push_content_woo_all_products( $pmab_meta );
			$this->push_content_woo_product( $pmab_meta );
			$this->push_content_woo_pro_category( $pmab_meta );
			$this->push_content_woo_pro_tags( $pmab_meta );
		}

		private function push_content_woo_shop( $pmab_meta ) {
			extract( $pmab_meta );
			if ( $tag === 'top' && PMAB_Content_Filter::check( 'is_shop' ) ) {
				add_filter(
					'woocommerce_archive_description',
					static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
						PMAB_Content::block_inject_div_with_script( $tag, $num_of_blocks, $p );
					},
					0
				);

			} else {
				add_filter(
					'woocommerce_after_shop_loop',
					static function ( $content ) use ( $p ) {
						PMAB_Content::output_injection( $p );
					},
					9999
				);
			}
		}

		private function push_content_woo_all_category_pages( $pmab_meta ) {
			$this->push_content_woo_category_page( $pmab_meta );
		}

		private function push_content_woo_category_page( $pmab_meta ) {
			extract( $pmab_meta );

			if ( $tag === 'top' ) {
				add_action(
					'woocommerce_archive_description',
					static function ( $content ) use ( $p, $tag, $num_of_blocks, $specific_woocategory ) {
						if ( is_product_category( $specific_woocategory ) ) {
							PMAB_Content::block_inject_div_with_script( $tag, $num_of_blocks, $p );
						}
					},
					0
				);

			} else {
				add_action(
					'woocommerce_after_shop_loop',
					static function ( $content ) use ( $p, $tag, $specific_woocategory ) {
						if ( is_product_category( $specific_woocategory ) ) {
							PMAB_Content::output_injection( $p );
						}
					},
					9999
				);
			}

		}

		/**
		 * Outputs injection content
		 *
		 * @param WP_Post $injection
		 */
		private static function output_injection( $injection ) {
			echo "<div style='clear:both;'>$injection->post_content</div>";
		}

		private function _push_content_product( $pmab_meta ) {
			extract( $pmab_meta );

			$hooks = [
				'top'                     => [ 'woocommerce_before_single_product', 0 ],
				'bottom'                  => [ 'woocommerce_after_single_product', 999 ],
				'before_add_to_cart_form' => [ 'woocommerce_before_add_to_cart_form', 0 ],
				'after_add_to_cart_form'  => [ 'woocommerce_after_add_to_cart_form', 999 ],
				'product_tabs'            => [ 'woocommerce_product_tabs', 0 ],
				'product_after_tabs'      => [ 'woocommerce_product_after_tabs', 999 ],
				'product_meta_start'      => [ 'woocommerce_product_meta_start', 0 ],
				'product_meta_end'        => [ 'woocommerce_product_meta_end', 999 ],
			];

			if ( ! empty( $hooks[ $tag ] ) ) {
				$hook = $hooks[ $tag ];
				add_filter(
					$hook[0],
					static function ( $param ) use ( $p ) {
						PMAB_Content::output_injection( $p );

						return $param;
					},
					$hook[1]
				);
			}
		}

		private function push_content_woo_all_products( $pmab_meta ) {
			extract( $pmab_meta );
			if ( is_singular( 'product' ) && ! $this->_post_id_in_list( get_post()->ID, $thisposts_exclude ) ) {
				$this->_push_content_product( $pmab_meta );
			}
		}

		private function push_content_woo_product( $pmab_meta ) {
			extract( $pmab_meta );
			if ( is_singular( 'product' ) && $this->_post_id_in_list( get_post()->ID, $specific_post ) ) {
				$this->_push_content_product( $pmab_meta );
			}
		}

		private function push_content_woo_pro_category( $pmab_meta ) {
			extract( $pmab_meta );

			if ( PMAB_Content_Filter::current_single_post_has_matching_terms( $woo_category, 'product_cat', $thisposts_exclude ) ) {
//				echo "<h6>$p->post_title : $inject_content_type [$tag] </h6>";

				$this->_push_content_product( $pmab_meta );
			}
		}

		private function push_content_woo_pro_tags( $pmab_meta ) {
			extract( $pmab_meta );
			if ( PMAB_Content_Filter::current_single_post_has_matching_terms( $tags, 'product_tag', $thisposts_exclude ) ) {
				$this->_push_content_product( $pmab_meta );
			}
		}

		// Inject Content to Specific Post, Page and CPT.

		/**
		 * @return void
		 */
		public function push_to_specific_content() {
			foreach ( $this->pmab_metas as $pmab_meta ) {
				extract( $pmab_meta );

				if ( empty( $tag_type ) || ! isset( $tag_type[0] ) || ! $dateandtime ) {
					continue;
				}

				add_filter(
					'the_content',
					static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
						return PMAB_Content_Filter::filter_hook( $content, $p, $tag, $num_of_blocks );
					},
					0
				);


//				echo "<pre>$p->post_title : $inject_content_type $tag</pre>";

				$callback = "push_content_$inject_content_type";
				if ( method_exists( $this, $callback ) ) {
					$this->$callback( $pmab_meta );
				}
			}
		}
	}
}
