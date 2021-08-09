<?php

/**
 * Helper Functions.
 *
 * @package BlockInjector
 */

if (!function_exists('pmab_select_checker')) {

    /**
     * @param mixed $val first compare value.
     * @param mixed $val2 second compare value.
     *
     * @return Boolean
     */
    function pmab_select_checker($val, $val2)
    {
        return $val == $val2 ? 'selected=selected' : '';
    }
}
if (!function_exists('pmab_update_content')) {
    /**
     * @param mixed $content The Content Description.
     * @param mixed $tag HTML tags.
     * @param mixed $num_of_blocks before, after blocks.
     * @param mixed $p post content.
     *
     * @return string
     */
    function pmab_update_content($content, $tag, $num_of_blocks, $p)
    {  
                if($num_of_blocks == 0 && $tag == 'p'){
                    return $content;
                }
        if ($tag == 'h2') {
            $re = '/<(h\d)>.*\<\/\1>/m';
            $str = $content;

            $i = -1;
            $d = preg_replace_callback($re, function ($matches) use (&$i, $num_of_blocks,$p) {
               $i += 1;
                if ($i == $num_of_blocks) {
                    return $matches[0].$p->post_content;
                }
                return $matches[0];
               
            }, $str);
            return $d;
            
            
        } else {
        // die($p->post_content);
            

            $content_array = explode("</$tag>", $content);
            
            array_splice($content_array, $num_of_blocks, 0, array($p->post_content));
            return implode("</$tag>", $content_array);
          

        }
    }
}

if (!function_exists('pmab_expire_checker')) {

    /**
     * @param String $starting_date Start Date and Time.
     * @param String $expiry_date Expire End Date and Time.
     *
     * @return Boolean
     */
    function pmab_expire_checker(String $starting_date, String $expiry_date)
    {
        $current_date = date('Y-m-d\TH:i'); // Date object using current date and time

        return ($starting_date <= $current_date) || ($expiry_date >= $current_date && $starting_date <= $current_date);
    }
}
if (!function_exists('pmab_push_to_specific_content')) {
    // Inject Content to Specific Post, Page and CPT.
    /**
     * @return void
     */
    function pmab_push_to_specific_content()
    {
		
		
        // loop over each post.
        foreach (get_posts(
            array(
                'post_type'      => 'block_injector',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            )
        ) as $p) {
            // echo "<pre>";
            // print_r($_REQUEST);
            // die("Anees");

            // get the meta you need form each post_pmab_meta_specific_post
            $num_of_blocks = get_post_meta($p->ID, '_pmab_meta_number_of_blocks', true);
            $tag_type      = get_post_meta($p->ID, '_pmab_meta_tag_n_fix', true);
            $startdate     = get_post_meta($p->ID, '_pmab_meta_startdate', true);
            $expiredate    = get_post_meta($p->ID, '_pmab_meta_expiredate', true);
            $dateandtime   = pmab_expire_checker($startdate, $expiredate);
            $inject_content_type   = get_post_meta($p->ID, '_pmab_meta_type', true);
            $specific_woocategory  = get_post_meta($p->ID, '_pmab_meta_specific_woocategory', true);
            $category         = get_post_meta($p->ID, '_pmab_meta_category', true);
            $specific_post         = get_post_meta($p->ID, '_pmab_meta_specific_post', true);
            $woo_category          = get_post_meta($p->ID, '_pmab_meta_woo_category', true);
            $tags                  = get_post_meta($p->ID, '_pmab_meta_tags', true);
            $specific_post         = is_string($specific_post) ? explode(',', $specific_post) : array();
            $woo_hooks             = get_post_meta($p->ID, '_pmab_meta_hook', true);


            $tag_type = is_string($tag_type) ? explode('_', $tag_type) : array();
            if (!empty($tag_type) && isset($tag_type[0]) && $dateandtime) {
                $tag = $tag_type[0];
                switch ($tag) {
                    case 'top':
                        $num_of_blocks = 0;
                        break;
                    case 'bottom':
                        $num_of_blocks = PHP_INT_MAX;
                        break;
                    case 'h2':
                        $num_of_blocks--;
                        break;
                    case 'woo':
                        $num_of_blocks = 0;
                        break;
                }	
                // var_dump(is_product_category());

				add_filter(
						'the_content',
						static function ($content) use ($p, $tag, $num_of_blocks) {
                            return pmab_filter_hook($content, $p, $tag, $num_of_blocks);
						},
						0
					);
                switch ($inject_content_type) {
                    case 'woo_all_pages':          
                        if($tag != 'top' && $tag != 'bottom' && $tag !='woo'){
                            add_filter(
                                'woocommerce_archive_description',
                                static function ($content) use ($p, $tag, $num_of_blocks) {
                                    if(is_shop() || is_product_category()){
                                        $num_of_blocks = $tag == "p" ? $num_of_blocks : $num_of_blocks + 1;
                                        echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> ".$p->post_content."</div>";
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
                        }
                        else if ($tag == 'woo') {
                            pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p);
                            
                        } 
                        else{
                            add_filter(
                                'woocommerce_archive_description',
                                static function ($content) use ($p, $tag, $num_of_blocks) {
                                    if (is_shop() || is_product_category()){
                                        echo $p->post_content; 
                                    }   
                                },
                                $num_of_blocks
                            );
                            add_filter(
                                'woocommerce_before_single_product',
                                static function ($content) use ($p, $tag, $num_of_blocks) {
                                    if (is_product() && $tag != 'bottom')  {
                                        echo $p->post_content;
                                        echo "<br>";
                                    }    
                                },
                                $num_of_blocks
                            );
                            
                            break;
                        }
                        break;
                    case 'woo_all_category_pages':
                            if($tag != 'top' && $tag != 'bottom' && $tag != 'woo'){
                                add_filter(
                                    'woocommerce_archive_description',
                                    static function ($content) use ($p, $tag, $num_of_blocks) {
                                        if(is_product_category()){
                                            $num_of_blocks = $tag == "p" ? $num_of_blocks  : $num_of_blocks + 1;
                                            echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> ".$p->post_content."</div>";
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
                            }
                            else if ($tag == 'woo') {
								add_filter(
									'woocommerce_breadcrumb',
									static function ($content) use ($p, $tag, $num_of_blocks, $woo_hooks) {
										if (is_product_category()){
											pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p);
										}   
									},
									0
								);
								
                                
                            } 
                            else{
                                add_filter(
                                    'woocommerce_archive_description',
                                    static function ($content) use ($p, $tag, $num_of_blocks) {
                                        if (is_product_category()){
                                            echo $p->post_content; 
                                        }   
                                    },
                                    $num_of_blocks
                                );
                            }
                            break;
                    case 'woo_category_page':
                        if($tag != 'top' && $tag != 'bottom' && $tag != 'woo'){
                            add_filter(
                                'woocommerce_archive_description',
                                static function ($content) use ($p, $tag, $num_of_blocks,$specific_woocategory) {
                                    if(is_product_category($specific_woocategory)){
                                        $num_of_blocks = $tag == "p" ? $num_of_blocks  : $num_of_blocks + 1;
                                        echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> ".$p->post_content."</div>";
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
                        }
                        else if ($tag == 'woo') {
							add_filter(
									'woocommerce_breadcrumb',
									static function ($content) use ($p, $tag, $num_of_blocks, $woo_hooks,$specific_woocategory) {
										if (is_product_category($specific_woocategory)){
											
											pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p);
										}   
									},
									0
								);
                        } 
                        else{
                            add_filter(
                                'woocommerce_archive_description',
                                static function ($content) use ($p, $tag, $specific_woocategory) {
                                    if (is_product_category($specific_woocategory)){
                                        echo $p->post_content; 
                                    }   
                                },
                                $num_of_blocks
                            );
                        }
                        
                        break;
                    case 'woo_shop':          
                        if($tag != 'top' && $tag != 'bottom' && $tag != 'woo'){
                            add_filter(
                                'woocommerce_archive_description',
                                static function ($content) use ($p, $tag, $num_of_blocks) {
                                    if (is_shop()){
                                        $num_of_blocks = $tag == "p" ? $num_of_blocks  : $num_of_blocks + 1;
                                        echo "<div data-tag='$tag' data-number_of_blocks='$num_of_blocks' class='block_inject_div'> ".$p->post_content."</div>";
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
                        }
                        else if ($tag == 'woo') {
							 add_filter(
                                'woocommerce_breadcrumb',
                                static function ($content) use ($tag,$num_of_blocks, $p,$woo_hooks) {
                                    if (is_shop()){
										 pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p);
                                        
                                    }   
                                },
                                0
                            );
                            break;
								 
                        } 
                        else{
                            add_filter(
                                'woocommerce_archive_description',
                                static function ($content) use ($p, $tag, $num_of_blocks) {
                                    if (is_shop()){
                                        echo $p->post_content; 
                                    }   
                                },
                                $num_of_blocks
                            );
                        }
                        break;
                    case 'woo_all_products': 
                        if ($tag === 'top') {
                            add_filter(
                                'woocommerce_before_single_product',
                                static function ($content) use ($p, $tag, $num_of_blocks) {
                                    echo $p->post_content;    
                                },
                                0
                            );
                        }
                        else if ($tag == 'woo') {
							add_filter(
                                'woocommerce_breadcrumb',
                                static function ($content) use ($p, $tag, $num_of_blocks,$woo_hooks) {
                                    if (is_product()){
										 pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p);
									 }    
                                },
                                0
                            );
                        } 
                        break;
                    case 'woo_product': 
                        if($tag === 'top'){
                            add_filter(
                                'woocommerce_before_single_product',
                                static function ($content) use ($p, $tag, $specific_post) {
                                    $wooposts             = get_posts(
                                        array(
                                            'post_type' => 'product',
                                            'posts_per_page' => -1,
                                            'post__in'      => $specific_post,
                                        )
                                    );
                                    foreach ($wooposts as $post) {
                                        if (is_single($post->ID)) {
                                        echo $p->post_content;
                                    }    
                                    }
                                },
                                0
                            );
                        }
                        else if ($tag == 'woo') {
							add_filter(
                                'woocommerce_breadcrumb',
                                static function ($content) use ($p, $tag, $num_of_blocks,$woo_hooks,$specific_post) {
                                   $wooposts             = get_posts(
                                        array(
                                            'post_type' => 'product',
                                            'posts_per_page' => -1,
                                            'post__in'      => $specific_post,
                                        )
                                    );
                                    foreach ($wooposts as $post) {
                                        if (is_single($post->ID)) {
                                         pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p);
                                        }    
                                    }   
                                },
                                0
                            );
							
							
                            break;
                        } 
                        break;
                    case 'woo_pro_category':
                        if($tag == 'top'){
							add_filter(
								'woocommerce_before_single_product',
								static function ($content) use ($p, $tag,$woo_category,$woo_hooks) {
									if (is_single()) {
										$categories = get_the_terms(get_post()->ID, 'product_cat');
										if ($categories) {
											foreach ($categories as $cat) {
												if ($cat->term_id == $woo_category) {
													if($tag === 'top'){
														echo $p->post_content; 
													}  

												}
											}
										}
									}

								},
								0
							);
						}  
						else if ($tag == 'woo') {
							add_filter(
								'woocommerce_breadcrumb',
								static function ($content) use ($p, $tag,$woo_category,$woo_hooks) {
									if (is_single()) {
										$categories = get_the_terms(get_post()->ID, 'product_cat');
										if ($categories) {
											foreach ($categories as $cat) {
												if ($cat->term_id == $woo_category) {
									 	pmab_custom_hook_content($woo_hooks, $p->post_content, $tag, $num_of_blocks, $p); 
												}
											}
										}
									}

								},
								0
							);
									

						} 
                    break;
                    case 'woo_tags':
                        if($tag === 'top'){
                            add_filter(
                                'woocommerce_before_single_product',
                                static function ($content) use ($p,$tags, $num_of_blocks) {
                                    $woo_tag_posts             = $tags ? get_posts(
                                        array(
                                            'post_type' => 'product',
                                            'posts_per_page' => -1,
                                            'tax_query' => array(
                                                array(
                                                    'taxonomy' => 'product_tag',
                                                    'terms' => explode(
                                                        ',',
                                                        $tags
                                                    ),
                                                )
                                            )
                                        )
                                    ) : array();
                                    foreach ($woo_tag_posts as $post) {
                                        if (is_single($post->ID)) {
                                            echo $p->post_content; 
                                        }
                                    }

                                        
                                },
                                0
                            );
                        } 
                        else if ($tag == 'woo') {
							
                            add_filter(
                                'woocommerce_breadcrumb',
                                static function ($content) use ($p,$tags, $num_of_blocks,$woo_hooks) {
											
									
                                   $woo_tag_posts             = $tags ? get_posts(
                                        array(
                                            'post_type' => 'product',
                                            'posts_per_page' => -1,
                                            'tax_query' => array(
                                                array(
                                                    'taxonomy' => 'product_tag',
                                                    'terms' => explode(
                                                        ',',
                                                        $tags
                                                    ),
                                                )
                                            )
                                        )
                                    ) : array();
									
                                    foreach ($woo_tag_posts as $post) {
										$tag =  'woo';
                                        if (is_single($post->ID)) {
                                          pmab_custom_hook_content($woo_hooks, $p->post_content ,$tag, $num_of_blocks, $p); 
 
                                        }
                                    }

                                        
                                },
                                0
                            );
                            
                        } 
                    break;
                                         
                    
                }
            }
        }
        
    }
}


if (!function_exists('pmab_filter_hook')) {
    // Content Filter Hook.

    /**
     * @param mixed $content
     * @param mixed $p
     * @param mixed $tag
     * @param mixed $num_of_blocks
     *
     * @return mixed
     */
	
    function pmab_filter_hook($content, $p, $tag, $num_of_blocks)
    {
		
		// die();
        $inject_content_type   = get_post_meta($p->ID, '_pmab_meta_type', true);
        $inject_content_type2  = get_post_meta($p->ID, '_pmab_meta_type2', true);
        $specific_post         = get_post_meta($p->ID, '_pmab_meta_specific_post', true);
        $specific_woocategory  = get_post_meta($p->ID, '_pmab_meta_specific_woocategory', true);
        $specific_post_exclude = get_post_meta($p->ID, '_pmab_meta_specific_post_exclude', true);
        $tags                  = get_post_meta($p->ID, '_pmab_meta_tags', true);
        $category              = get_post_meta($p->ID, '_pmab_meta_category', true);
        $woo_category          = get_post_meta($p->ID, '_pmab_meta_woo_category', true);
        $tag_type              = get_post_meta($p->ID, '_pmab_meta_tag_n_fix', true);

        $woo_hooks             = get_post_meta($p->ID, '_pmab_meta_hook', true);

        $tag_posts             = $tags ? get_posts(
            array(
                'posts_per_page' => -1,
                'tag__in'        => explode(
                    ',',
                    $tags
                ),
            )
        ) : array();
        $woo_tag_posts             = $tags ? get_posts(
            array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_tag',
                        'field' => 'id',
                        'terms' => explode(
                            ',',
                            $tags
                        ),
                    )
                )
            )
        ) : array();
        $thisposts_exclude     = is_string($specific_post_exclude) ? explode(',', $specific_post_exclude) : array();
        $specific_post         = is_string($specific_post) ? explode(',', $specific_post) : array();
        $specific_woocategory  = is_string($specific_woocategory) ? explode(',', $specific_woocategory) : array();
		
        if ($inject_content_type == 'page') {
            $thisposts             = get_pages(
                array(
                    'posts_per_page' => -1,
                    'include'      => $specific_post,
                )
            );
        } else {
            $thisposts             = get_posts(
                array(
                    'posts_per_page' => -1,
                    'post__in'      => $specific_post,
                )
            );
        }
        $wooposts             = get_posts(
            array(
                'post_type' => 'product',
                'posts_per_page' => -1,
                'post__in'      => $specific_post,
            )
        );
        
        // Check if we're inside the main loop in a single Post.
        switch ($inject_content_type) {
				
            case 'woo_all_pages':
                if (class_exists('wooCommerce') && (is_woocommerce() || is_front_page() || is_checkout() || is_account_page() || is_cart() || is_shop())) {
                    if ($tag_type === 'woo_hook') {
//                         pmab_custom_hook_content($woo_hooks, $content, $tag, $num_of_blocks, $p);
                        break;
                    } else if($tag == 'top' && (is_shop() || is_product())){
                        break;
                    }         
                    else{             
                         return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'both', $content, $tag, $num_of_blocks, $p);
                       
                    }
                }
                break;
            case 'woo_all_category_pages':
                if (function_exists('is_product_category') &&  is_product_category()) {
                    if ($tag_type === 'woo_hook') {
                        pmab_custom_hook_content($woo_hooks, $content, $tag, $num_of_blocks, $p);
                        break;
                    } else {
                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                    }
                }
                break;
            case 'woo_category_page':
                if (function_exists('is_product_category') &&  is_product_category($specific_woocategory)) {
                    if ($tag_type === 'woo_hook') {
                        pmab_custom_hook_content($woo_hooks, $content, $tag, $num_of_blocks, $p);
                        break;
                    } else {

                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                    }
                }
                break;
            case 'woo_checkout':
                if ((function_exists('is_checkout') && is_checkout())) {
                    if ($tag_type === 'woo_hook') {
                        pmab_custom_hook_content($woo_hooks, $content, $tag, $num_of_blocks, $p);
                        break;
                    } else {
                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                    }
                }
                break;
            case 'woo_account':
                if (function_exists('is_account_page') && is_account_page()) {
                    if ($tag_type === 'woo_hook') {
                        pmab_custom_hook_content($woo_hooks, $content, $tag, $num_of_blocks, $p);
                        break;
                    } else {
                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                    }
                }
                break;
            case 'woo_basket':
                if ((function_exists('is_cart') &&  is_cart())) {
                    if ($tag_type === 'woo_hook') { 
                        pmab_custom_hook_content($woo_hooks, $content, $tag, $num_of_blocks, $p);
                        break;
                    } else { 
                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                    }
                }
                break;
            case 'woo_shop':
				
                if (function_exists('is_shop') && is_shop()) {

					
                    if ($tag_type === 'woo_hook') {				
                        break;
                    } else if ($tag === 'bottom') {
		                
						
                        pmab_custom_hook_content('woocommerce_after_shop_loop', $content, $tag, 1, $p);		
                        break;
                    } else {	

                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                        break;
                    }
                }
                break;
            case 'tags':
                return pmab_posts_filter_content($tag_posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single');
            case 'woo_tags':
                if ($tag_type === 'woo_hook') {
                    break;
                }
                else if($tag === 'top'){
                    
                }
                else {

                    return pmab_posts_filter_content($woo_tag_posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single');
                }
                break;
             case 'category':
                if (is_single()) {
                    $categories = wp_get_post_categories(get_post()->ID);
                    
                    foreach ($categories as $cat) {
                        if (in_array( $cat ,$category)) {
                            if ($inject_content_type2 === 'post_exclude' && in_array(get_post()->ID, $thisposts_exclude, false)) {
                                return $content;
                            } else {
                                return pmab_update_content($content, $tag, $num_of_blocks, $p);
                            }
                        }
                    }
                }
                break;
            case 'woo_pro_category':
                if (is_single()) {
                    $categories = get_the_terms(get_post()->ID, 'product_cat');
                    if ($categories) {
                        foreach ($categories as $cat) {
                            if ($cat->term_id == $woo_category) {

                                if ($inject_content_type2 === 'post_exclude' && in_array(get_post()->ID, $thisposts_exclude, false)) {
                                    return $content;
                                } else if ($tag === 'top') {

                                    break;
                                } else {
                                    if ($tag_type === 'woo_hook') {
                                        break;
                                    } else {
                                        return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                    }
                                }
                            }
                        }
                    }
                }
                break;
            case 'post':
                return pmab_posts_filter_content($thisposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single');
                break;
            case 'page':
                return pmab_posts_filter_content($thisposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_page');
                break;
            case 'all_post':
                if (is_single() && (!is_woocommerce() && !is_product() && !is_shop() && !is_product_category())){
                    return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'post_exclude', $content, $tag, $num_of_blocks, $p);
                }
                break;
            case 'all_page':
                if (is_page()) {
                    return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                }
                break;
            case 'post_page':
                if (is_page() || is_single()) {
                    return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'both', $content, $tag, $num_of_blocks, $p);
                }
                break;
            case 'woo_all_products':
                if (is_product()) {
                    if ($tag_type === 'woo_hook') {
                        
						break;
                    } 
                    else if($tag === 'top'){
                        
                        break;
                    }else {
                        
                        return pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'post_exclude', $content, $tag, $num_of_blocks, $p);
                    }
                    break;
                }
            case 'woo_product':
                if (is_product()) {
                    if ($tag_type === 'woo_hook') {
                       
                        break;
                    } 
                    else if($tag === 'top'){
                        break;
                    } else {
                        return pmab_posts_filter_content($wooposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, 'is_single');
                    }
                    break;
                }
        }

        return $content;
    }
}

if (!function_exists('pmab_posts_filter_content')) {
    // Make All filter Content
    /**
     * @param mixed $posts
     * @param mixed $thisposts_exclude
     * @param mixed $inject_content_type2
     * @param mixed $content
     * @param mixed $tag
     * @param mixed $num_of_blocks
     * @param mixed $p
     * @param mixed $function_name
     *
     * @return mixed
     */
    function pmab_posts_filter_content($posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, $function_name)
    {
        foreach ($posts as $post) {
            if ($function_name($post->ID)) {

                if ($inject_content_type2 === 'post_exclude' && !in_array($post->ID, $thisposts_exclude, true)) {

                    if ($tag == 'top' && is_product()) {
                        pmab_custom_hook_content('woocommerce_before_single_product', $content, $tag, 1, $p);
                        return;
                    } else {
                        return pmab_update_content($content, $tag, $num_of_blocks, $p);
                    }
                }
                if ($tag == 'top' && is_product()) {
                    pmab_custom_hook_content('woocommerce_before_single_product', $content, $tag, 1, $p);
                    return;
                } else {
                    return pmab_update_content($content, $tag, $num_of_blocks, $p);
                }
            }
        }
        return $content;
    }
}
if (!function_exists('pmab_custom_hook_content')) {
    // Make Custom Hook Content
    /**
     * @param mixed $custom_hook
     * @param mixed $content
     * @param mixed $tag
     * @param mixed $num_of_blocks
     * @param mixed $p
     *
     * @return mixed
     */
    function pmab_custom_hook_content($custom_hook, $content, $tag, $num_of_blocks, $p)
    {   

      

        add_action(
            $custom_hook,
            static function ($content) use ($p, $tag, $num_of_blocks) {
                echo pmab_update_content($content, $tag, $num_of_blocks, $p);;
            },
            0
        );
    }
}
function print_filters_for( $hook = '' ) {

    global $wp_filter;
    if( empty( $hook ) || !isset( $wp_filter[$hook] ) ){
        return false;
    }
    else{
        return true;
    }
}
if (!function_exists('pmab_filter_exclude_content')) {
    // Exclude Filter Content
    /**
     * @param mixed $thisposts_exclude
     * @param mixed $inject_content_type2
     * @param mixed $exclude_type
     * @param mixed $content
     * @param mixed $tag
     * @param mixed $num_of_blocks
     * @param mixed $p
     *
     * @return mixed
     */
    function pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, $exclude_type, $content, $tag, $num_of_blocks, $p)
    {
        if ($exclude_type === 'both') {
            $excludes = array('post_exclude', 'page_exclude');
            foreach ($excludes as $exclude) {
                if ($inject_content_type2 === $exclude &&  in_array(get_post()->ID, $thisposts_exclude, false)) {
                    return $content;
                }
            }
            if ($tag == 'top' && is_product()) {
                pmab_custom_hook_content('woocommerce_before_single_product', $content, $tag, 1, $p);
                return;
            } else {
                return pmab_update_content($content, $tag, $num_of_blocks, $p);
            }
        }
        if ($inject_content_type2 === $exclude_type && in_array(get_post()->ID, $thisposts_exclude, false)) {
            return $content;
        }

        if ($tag == 'top' && is_product()) {
            pmab_custom_hook_content('woocommerce_before_single_product', $content, $tag, 1, $p);
            return;
        } else {
            return pmab_update_content($content, $tag, $num_of_blocks, $p);
        }
    }
}
