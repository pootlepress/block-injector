<?php

/**
 * Content class.
 *
 * @package BlockInjector
 */

if ( ! class_exists( 'PMAB_Content' ) ) {
    class PMAB_Content {

        private $pmab_metas = Array();

        public function __construct() {
            $this->extract_block_injectors_with_metas();
        }

        private function extract_block_injectors_with_metas() {
            foreach (
                get_posts(
                    array(
                        'post_type'      => 'block_injector',
                        'post_status'    => 'publish',
                        'posts_per_page' => - 1,
                    )
                ) as $p
            ) {
                // get the meta you need form each post_pmab_meta_specific_post
                $pmab_meta = array(
                    'p' => $p,
                    'num_of_blocks' => get_post_meta( $p->ID, '_pmab_meta_number_of_blocks', true ),
                    'tag_type' => get_post_meta( $p->ID, '_pmab_meta_tag_n_fix', true ),
                    'startdate' => get_post_meta( $p->ID, '_pmab_meta_startdate', true ),
                    'expiredate' => get_post_meta( $p->ID, '_pmab_meta_expiredate', true ),
                    'inject_content_type' => get_post_meta( $p->ID, '_pmab_meta_type', true ),
                    'specific_woocategory' => get_post_meta( $p->ID, '_pmab_meta_specific_woocategory', true ),
                    'category' => get_post_meta( $p->ID, '_pmab_meta_category', true ),
                    'woo_category' => get_post_meta( $p->ID, '_pmab_meta_woo_category', true ),
                    'tags' => get_post_meta( $p->ID, '_pmab_meta_tags', true ),
                    'woo_hooks' => get_post_meta( $p->ID, '_pmab_meta_hook', true ),
                );

                $specific_post = get_post_meta( $p->ID, '_pmab_meta_specific_post', true );
                $pmab_meta['specific_post'] = is_string( $specific_post ) ? explode( ',', $specific_post ) : array();
                $pmab_meta['dateandtime'] = pmab_expire_checker( $pmab_meta['startdate'], $pmab_meta['expiredate'] );

                $num_of_blocks = get_post_meta( $p->ID, '_pmab_meta_number_of_blocks', true );
                $tag_type = get_post_meta( $p->ID, '_pmab_meta_tag_n_fix', true );
                $tag_type = is_string( $tag_type ) ? explode( '_', $tag_type ) : array();

                if (! empty ($tag_type) && isset( $tag_type[0] )) {
                    $tag = $tag_type[0];
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
                
                array_push( $this->pmab_metas, $pmab_meta);
            }
        }

        private function woo_all_pages($pmab_meta) {
            extract($pmab_meta);
            if ( $tag == 'woo' ) {
                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks, $woo_hooks ) {
                        if ( is_product_category() ) {
                            pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                        }
                    },
                    0
                );
            } else if ( ! in_array( $tag, array('top', 'bottom') ) ) {
                add_filter(
                    'woocommerce_archive_description',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
                        if ( is_product_category() ) {
                            $num_of_blocks = $tag == "p" ? $num_of_blocks : $num_of_blocks + 1;
                            echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> " . $p->post_content . "</div>";
                            echo '<script>window.onload = function(){
                                        document.querySelectorAll(".block_inject_div").forEach((d,k)=>{
                                        let tags = d.dataset.tag === "h2" ? ".page-description h1,.page-description  h2,.page-description h3,.page-description h4,.page-description h5,.page-description h6" : ".page-description p";
                                        document.querySelectorAll(tags).forEach((v,k)=>{k += 1;
                                        if(d.dataset.number_of_blocks == k) {
                                            v.after(d);
                                        }

                                        })

                                    })
                                }</script>';
                        }
                    },
                    0
                );
            } else {
                add_filter(
                    'woocommerce_archive_description',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
                        if ( is_product_category() ) {
                            echo $p->post_content;
                        }
                    },
                    $num_of_blocks
                );
            }
        }

        private function woo_shop($pmab_meta) {
            extract($pmab_meta);
            if ( $tag == 'woo' ) {
                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $tag, $num_of_blocks, $p, $woo_hooks ) {
                        if ( is_shop() ) {
                            pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                        }
                    },
                    0
                );
            } else if ( ! in_array( $tag, array('top', 'bottom') ) ) {
                add_filter(
                    'woocommerce_archive_description',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
                        if ( is_shop() ) {
                            $num_of_blocks = $tag == "p" ? $num_of_blocks : $num_of_blocks + 1;
                            echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> " . $p->post_content . "</div>";
                            echo '<script>window.onload = function(){
                                    document.querySelectorAll(".block_inject_div").forEach((d,k)=>{
                                    let tags = d.dataset.tag === "h2" ? ".page-description h1,.page-description  h2,.page-description h3,.page-description h4,.page-description h5,.page-description h6" : ".page-description p";
                                    document.querySelectorAll(tags).forEach((v,k)=>{k += 1;
                                    if(d.dataset.number_of_blocks == k) {
                                        v.after(d);
                                    }

                                    })

                                })
                            }</script>';
                        }
                    },
                    0
                );
            
            } else {
                add_filter(
                    'woocommerce_archive_description',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
                        if ( is_shop() ) {
                            echo $p->post_content;
                        }
                    },
                    $num_of_blocks
                );
            }
        }

        private function woo_category_pages($pmab_meta) {
            extract($pmab_meta);
            if ( $tag == 'woo' ) {
                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks, $woo_hooks, $specific_woocategory ) {
                        if ( is_product_category( $specific_woocategory ) ) {
                            pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                        }
                    },
                    0
                );
            } else if ( ! in_array( $tag, array('top', 'bottom') ) ) {
                add_filter(
                    'woocommerce_archive_description',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks, $specific_woocategory ) {
                        if ( is_product_category( $specific_woocategory ) ) {
                            $num_of_blocks = $tag == "p" ? $num_of_blocks : $num_of_blocks + 1;
                            echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> " . $p->post_content . "</div>";
                            echo '<script>window.onload = function(){
                                    document.querySelectorAll(".block_inject_div").forEach((d,k)=>{
                                    let tags = d.dataset.tag === "h2" ? ".page-description h1,.page-description  h2,.page-description h3,.page-description h4,.page-description h5,.page-description h6" : ".page-description p";
                                    document.querySelectorAll(tags).forEach((v,k)=>{k += 1;
                                    if(d.dataset.number_of_blocks == k) {
                                        v.after(d);
                                    }

                                    })

                                })
                            }</script>';
                        }
                    },
                    0
                );

            } else {
                add_filter(
                    'woocommerce_archive_description',
                    static function ( $content ) use ( $p, $tag, $specific_woocategory ) {
                        if ( is_product_category( $specific_woocategory ) ) {
                            echo $p->post_content;
                        }
                    },
                    $num_of_blocks
                );
            }

        }

        private function woo_all_products($pmab_meta) {
            extract($pmab_meta);
            if ( $tag === 'top' ) {
                add_filter(
                    'woocommerce_before_single_product',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks ) {
                        echo $p->post_content;
                    },
                    0
                );
            } else if ( $tag == 'woo' ) {
                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks, $woo_hooks ) {
                        if ( is_product() ) {
                            pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                        }
                    },
                    0
                );
            }
        }

        private function woo_product($pmab_meta) {
            extract($pmab_meta);
            if ( $tag === 'top' ) {
                add_filter(
                    'woocommerce_before_single_product',
                    static function ( $content ) use ( $p, $tag, $specific_post ) {
                        $wooposts = get_posts(
                            array(
                                'post_type'      => 'product',
                                'posts_per_page' => - 1,
                                'post__in'       => $specific_post,
                            )
                        );
                        foreach ( $wooposts as $post ) {
                            if ( is_single( $post->ID ) ) {
                                echo $p->post_content;
                            }
                        }
                    },
                    0
                );
            } else if ( $tag == 'woo' ) {
                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks, $woo_hooks, $specific_post ) {
                        $wooposts = get_posts(
                            array(
                                'post_type'      => 'product',
                                'posts_per_page' => - 1,
                                'post__in'       => $specific_post,
                            )
                        );
                        foreach ( $wooposts as $post ) {
                            if ( is_single( $post->ID ) ) {
                                pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                            }
                        }
                    },
                    0
                );
            }
        }

        private function woo_pro_category($pmab_meta) {
            extract($pmab_meta);
            if ( $tag == 'top' ) {
                add_filter(
                    'woocommerce_before_single_product',
                    static function ( $content ) use ( $p, $tag, $woo_category, $woo_hooks ) {
                        if ( is_single() ) {
                            $categories = get_the_terms( get_post()->ID, 'product_cat' );
                            if ( $categories ) {
                                foreach ( $categories as $cat ) {
                                    if ( $cat->term_id == $woo_category ) {
                                        if ( $tag === 'top' ) {
                                            echo $p->post_content;
                                        }

                                    }
                                }
                            }
                        }

                    },
                    0
                );
            } else if ( $tag == 'woo' ) {
                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $p, $tag, $woo_category, $woo_hooks ) {
                        if ( is_single() ) {
                            $categories = get_the_terms( get_post()->ID, 'product_cat' );
                            if ( $categories ) {
                                foreach ( $categories as $cat ) {
                                    if ( $cat->term_id == $woo_category ) {
                                        pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                                    }
                                }
                            }
                        }
                    },
                    0
                );
            }
        }

        private function woo_tags($pmab_meta) {
            extract($pmab_meta);
            if ( $tag === 'top' ) {
                add_filter(
                    'woocommerce_before_single_product',
                    static function ( $content ) use ( $p, $tags, $num_of_blocks ) {
                        $woo_tag_posts = $tags ? get_posts(
                            array(
                                'post_type'      => 'product',
                                'posts_per_page' => - 1,
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => 'product_tag',
                                        'terms'    => explode(
                                            ',',
                                            $tags
                                        ),
                                    )
                                )
                            )
                        ) : array();
                        foreach ( $woo_tag_posts as $post ) {
                            if ( is_single( $post->ID ) ) {
                                echo $p->post_content;
                            }
                        }
                    },
                    0
                );
            } else if ( $tag == 'woo' ) {

                add_filter(
                    'woocommerce_breadcrumb',
                    static function ( $content ) use ( $p, $tags, $num_of_blocks, $woo_hooks ) {

                        $woo_tag_posts = $tags ? get_posts(
                            array(
                                'post_type'      => 'product',
                                'posts_per_page' => - 1,
                                'tax_query'      => array(
                                    array(
                                        'taxonomy' => 'product_tag',
                                        'terms'    => explode(
                                            ',',
                                            $tags
                                        ),
                                    )
                                )
                            )
                        ) : array();

                        foreach ( $woo_tag_posts as $post ) {
                            $tag = 'woo';
                            if ( is_single( $post->ID ) ) {
                                pmab_custom_hook_content( $woo_hooks, $p->post_content, $tag, $num_of_blocks, $p );
                            }
                        }
                    },
                    0
                );

            }
        }

        static private function extract_meta_for_filter_hook($p) {
            $tags = get_post_meta( $p->ID, '_pmab_meta_tags', true );
            return array(
                'inject_content_type'   => get_post_meta( $p->ID, '_pmab_meta_type', true ),
                'inject_content_type2'  => get_post_meta( $p->ID, '_pmab_meta_type2', true ),
                'specific_post'         => get_post_meta( $p->ID, '_pmab_meta_specific_post', true ),
                'specific_woocategory'  => get_post_meta( $p->ID, '_pmab_meta_specific_woocategory', true ),
                'specific_post_exclude' => get_post_meta( $p->ID, '_pmab_meta_specific_post_exclude', true ),
                'tags'                  => $tags,
                'category'              => get_post_meta( $p->ID, '_pmab_meta_category', true ),
                'woo_category'          => get_post_meta( $p->ID, '_pmab_meta_woo_category', true ),
                'tag_type'              => get_post_meta( $p->ID, '_pmab_meta_tag_n_fix', true ),
                'woo_hooks'             => get_post_meta( $p->ID, '_pmab_meta_hook', true ),
                'tag_posts'             => $tags ? get_posts(
                    array(
                        'posts_per_page' => - 1,
                        'tag__in'        => explode(
                            ',',
                            $tags
                        ),
                    )
                ) : array(),
                'woo_tag_posts'          => $tags ? get_posts(
                    array(
                        'post_type'      => 'product',
                        'posts_per_page' => - 1,
                        'tax_query'      => array(
                            array(
                                'taxonomy' => 'product_tag',
                                'field'    => 'id',
                                'terms'    => explode(
                                    ',',
                                    $tags
                                ),
                            )
                        )
                    )
                ) : array(),
            );
        }

        private function check($function, $add_param) {
            if ( is_null( $add_param ) ) {
                return function_exists( $function ) && $$function();
            }
            return function_exists( $function ) && $$function( $add_param );
        }

        // Content Filter Hook.
        /**
         * @param mixed $content
         * @param mixed $p
         * @param mixed $tag
         * @param mixed $num_of_blocks
         *
         * @return mixed
         */
         static public function filter_hook( $content, $p, $tag, $num_of_blocks ) {
            extract( self::extract_meta_for_filter_hook( $p ) );
            $thisposts_exclude    = is_string( $specific_post_exclude ) ? explode( ',', $specific_post_exclude ) : array();
            $specific_post        = is_string( $specific_post ) ? explode( ',', $specific_post ) : array();
            $specific_woocategory = is_string( $specific_woocategory ) ? explode( ',', $specific_woocategory ) : array();
    
            $type = ($inject_content_type == 'page' ) ? 'include' : 'post__in';

            $thisposts = get_pages(
                array(
                    'posts_per_page' => - 1,
                    $type            => $specific_post,
                )
            );

            $wooposts = get_posts(
                array(
                    'post_type'      => 'product',
                    'posts_per_page' => - 1,
                    'post__in'       => $specific_post,
                )
            );
    
            $checks = array(
                'woo_all_category_pages' => array( 'is_product_category', NULL),
                'woo_category_page' => array( 'is_product_category', $specific_woocategory ),
                'woo_checkout' => array( 'is_checkout', NULL ),
                'woo_account' => array( 'is_account_page', NULL ),
                'woo_basket' => array( 'is_cart', NULL ),
            );

            // Check if we're inside the main loop in a single Post.
            if ( array_key_exists($inject_content_type, $checks) ) {
                $variables = $checks[$inject_content_type];
                if ( $this->check( $variables[0], $variables[1] ) ) {
                    if ( $tag_type === 'woo_hook' ) {
                        pmab_custom_hook_content( $woo_hooks, $content, $tag, $num_of_blocks, $p );
                    } else {
                        return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p );
                    }
                }
            }
            
            switch ( $inject_content_type ) {
                case 'woo_all_pages':
                    if ( class_exists( 'wooCommerce' ) && ( is_woocommerce() || is_front_page() || is_checkout() || is_account_page() || is_cart() || is_shop() ) ) {
                        if ( $tag_type === 'woo_hook' ) {
                            break;
                        } else if ( $tag == 'top' && ( is_shop() || is_product() ) ) {
                            break;
                        } else {
                            return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'both', $content, $tag, $num_of_blocks, $p );
                        }
                    }
                    break;
                case 'woo_shop':
                    if ( $this->check( 'is_shop' ) ) {
    
                        if ( $tag_type === 'woo_hook' ) {
                            break;
                        } else if ( $tag === 'bottom' ) {
    
    
                            pmab_custom_hook_content( 'woocommerce_after_shop_loop', $content, $tag, 1, $p );
                            break;
                        } else {
    
                            return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p );
                            break;
                        }
                    }
                    break;
                case 'tags':
                    return pmab_posts_filter_content( $tag_posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single' );
                case 'woo_tags':
                    if ( $tag_type === 'woo_hook' ) {
                        break;
                    } else if ( $tag === 'top' ) {
    
                    } else {
    
                        return pmab_posts_filter_content( $woo_tag_posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single' );
                    }
                    break;
                case 'category':
                    if ( is_single() ) {
                        $categories = wp_get_post_categories( get_post()->ID );
    
                        foreach ( $categories as $cat ) {
                            if ( in_array( $cat, $category ) ) {
                                if ( $inject_content_type2 === 'post_exclude' && in_array( get_post()->ID, $thisposts_exclude, false ) ) {
                                    return $content;
                                } else {
                                    return pmab_update_content( $content, $tag, $num_of_blocks, $p );
                                }
                            }
                        }
                    }
                    break;
                case 'woo_pro_category':
                    if ( is_single() ) {
                        $categories = get_the_terms( get_post()->ID, 'product_cat' );
                        if ( $categories ) {
                            foreach ( $categories as $cat ) {
                                if ( $cat->term_id == $woo_category ) {
    
                                    if ( $inject_content_type2 === 'post_exclude' && in_array( get_post()->ID, $thisposts_exclude, false ) ) {
                                        return $content;
                                    } else if ( $tag === 'top' ) {
    
                                        break;
                                    } else {
                                        if ( $tag_type === 'woo_hook' ) {
                                            break;
                                        } else {
                                            return pmab_update_content( $content, $tag, $num_of_blocks, $p );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    break;
                case 'post':
                    return pmab_posts_filter_content( $thisposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single' );
                    break;
                case 'page':
                    return pmab_posts_filter_content( $thisposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_page' );
                    break;
                case 'all_post':
                    if ( is_single() && ( ! is_woocommerce() && ! is_product() && ! is_shop() && ! is_product_category() ) ) {
                        return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'post_exclude', $content, $tag, $num_of_blocks, $p );
                    }
                    break;
                case 'all_page':
                    if ( is_page() ) {
                        return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p );
                    }
                    break;
                case 'post_page':
                    if ( is_page() || is_single() ) {
                        return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'both', $content, $tag, $num_of_blocks, $p );
                    }
                    break;
                case 'woo_all_products':
                    if ( is_product() ) {
                        if ( $tag_type === 'woo_hook' ) {
    
                            break;
                        } else if ( $tag === 'top' ) {
    
                            break;
                        } else {
    
                            return pmab_filter_exclude_content( $thisposts_exclude, $inject_content_type2, 'post_exclude', $content, $tag, $num_of_blocks, $p );
                        }
                        break;
                    }
                case 'woo_product':
                    if ( is_product() ) {
                        if ( $tag_type === 'woo_hook' ) {
    
                            break;
                        } else if ( $tag === 'top' ) {
                            break;
                        } else {
                            return pmab_posts_filter_content( $wooposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single' );
                        }
                        break;
                    }
            }
    
            return $content;
        }

        // Inject Content to Specific Post, Page and CPT.
        /**
         * @return void
         */
        public function push_to_specific_content() {
            foreach ( $this->pmab_metas as $pmab_meta) {
                extract($pmab_meta);
                if ( empty( $tag_type ) || ! isset( $tag_type[0] ) || ! $dateandtime ) {
                    continue;
                }
                $that = $this;
                add_filter(
                    'the_content',
                    static function ( $content ) use ( $p, $tag, $num_of_blocks, $that ) {
                        return PMAB_Content::filter_hook( $content, $p, $tag, $num_of_blocks );
                    },
                    0
                );
                if (method_exists( $this, $inject_content_type ) ) {
                     $this->$$inject_content_type($pmab_meta);
                }

            }
        }
    }
}
