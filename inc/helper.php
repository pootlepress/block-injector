<?php
if (!function_exists('pmab_select_checker')) {
    function pmab_select_checker($val, $val2)
    {
        return $val === $val2 ? 'selected=selected' : '';
    }
}
if (!function_exists('pmab_update_content')) {
    function pmab_update_content($content, $tag, $num_of_blocks, $p)
    {
        $content_array = explode("</$tag>", $content);
        array_splice($content_array, $num_of_blocks, 0, array($p->post_content));
        $update_content = implode("</$tag>", $content_array);
        return $update_content;
    }
}

if (!function_exists('pmab_expire_checker')) {
    function pmab_expire_checker($startingdate, $expirydate)
    {
        $currentdate  = date('Y-m-d\TH:i', time()); // Date object using current date and time
        if ((($startingdate == '' && $expirydate == '') || $startingdate <= $currentdate) || ($expirydate >= $currentdate && $startingdate <= $currentdate)) {
            return true;
        }
        return false;
    }
}
if (!function_exists('pmab_push_to_specific_content')) {
    /**
     * Push Block Injector Post into Post,Page and CPT.
     *
     * @return void
     */

    function pmab_push_to_specific_content()
    {
        // code...
        $posts = get_posts(
            array(
                'post_type'      => 'block_injector',
                'post_status'    => 'publish',
                'posts_per_page' => -1,
            )
        );

        // loop over each post
        foreach ($posts as $p) {

            $post_id = $p->ID;
            // get the meta you need form each post_pmab_meta_specific_post
            $num_of_blocks        = get_post_meta($post_id, '_pmab_meta_number_of_blocks', true);
            $specific_post        = get_post_meta($post_id, '_pmab_meta_specific_post', true);
            $specific_post_exclude = get_post_meta($post_id, '_pmab_meta_specific_post_exclude', true);
            $tags                 = get_post_meta($post_id, '_pmab_meta_tags', true);
            $tag_type             = get_post_meta($post_id, '_pmab_meta_tag_n_fix', true);
            $inject_content_type  = get_post_meta($post_id, '_pmab_meta_type', true);
            $inject_content_type2 = get_post_meta($post_id, '_pmab_meta_type2', true);
            $startdate            = get_post_meta($post_id, '_pmab_meta_startdate', true);
            $expiredate           = get_post_meta($post_id, '_pmab_meta_expiredate', true);
            $category             = get_post_meta($post_id, '_pmab_meta_category', true);
            $is_post = false;
            if ($tags) {
                $tag_ids = explode(',', $tags);
                $args = array(
                    'tag__in' => $tag_ids
                );
                $is_post = get_posts($args);
            }
            $dateandtime = pmab_expire_checker($startdate, $expiredate);
            $thisposts_exclude = is_string($specific_post_exclude) ? explode(',', $specific_post_exclude) : [];
            $thisposts = is_string($specific_post) ?  explode(',', $specific_post) : [];
            $tag_type = is_string($tag_type) ?  explode('_', $tag_type) : [];
            if (!empty($tag_type) && isset($tag_type[0]) && isset($tag_type[1])) {
                $tag          = $tag_type[0];

                add_filter(
                    'the_content',
                    function ($content) use ($inject_content_type, $inject_content_type2, $p, $tag, $num_of_blocks, $category, $thisposts_exclude, $thisposts, $is_post, $dateandtime) {
                        $num_of_blocks = $tag == 'top' ? 0 : PHP_INT_MAX;

                        // Check if we're inside the main loop in a single Post.
                        if ($inject_content_type == 'tags' && $is_post) {
                            foreach ($is_post as $is_posts) {
                                if ($dateandtime && is_single($is_posts->ID)) {
                                    if ($inject_content_type2 == 'post_exclude' && !in_array($is_posts->ID, $thisposts_exclude)) {
                                        return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                    }
                                    return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                }
                            }
                        }
                        if ($inject_content_type == 'category' && $dateandtime && is_single()) {
                            $categories = wp_get_post_categories(get_post()->ID);
                            foreach ($categories as $cat) {
                                if ($cat == $category) {
                                    if ($inject_content_type2 == 'post_exclude' && !in_array(get_post()->ID, $thisposts)) {
                                        return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                    } else if (is_single(get_post()->ID)) {
                                        return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                    }
                                }
                            }
                        }
                        if ($inject_content_type == 'post') {
                            foreach ($thisposts as $thispost) {
                                $currentpost = get_post(intval($thispost));
                                if ($currentpost  && $dateandtime && is_single($currentpost->ID)) {
                                    return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                }
                            }
                        }
                        if ($inject_content_type == 'page') {
                            foreach ($thisposts as $thispage) {
                                $currentpage = get_post($thispage);
                                if ($currentpage && $dateandtime && is_page($currentpage->ID)) {
                                    return pmab_update_content($content, $tag, $num_of_blocks, $p);
                                }
                            }
                        }

                        if ($inject_content_type == 'all_post' && $dateandtime && is_single()) {
                            if ($inject_content_type2 == 'post_exclude' && !in_array(get_post()->ID, $thisposts_exclude)) {
                                return pmab_update_content($content, $tag, $num_of_blocks, $p);
                            }
                            return pmab_update_content($content, $tag, $num_of_blocks, $p);
                        }

                        if ($inject_content_type == 'all_page' && $dateandtime && is_page()) {
                            if ($inject_content_type2 == 'page_exclude' && !in_array(get_post()->ID, $thisposts_exclude)) {
                                return pmab_update_content($content, $tag, $num_of_blocks, $p);
                            }
                            return pmab_update_content($content, $tag, $num_of_blocks, $p);
                        }
                        if ($inject_content_type == 'post_page' && $dateandtime && (is_page() || is_single())) {
                            if ($inject_content_type2 == 'page_exclude' && !in_array(get_post()->ID, $thisposts_exclude)) {
                                return pmab_update_content($content, $tag, $num_of_blocks, $p);
                            } else if ($inject_content_type2 == 'post_exclude' && !in_array(get_post()->ID, $thisposts_exclude)) {
                                return pmab_update_content($content, $tag, $num_of_blocks, $p);
                            }
                            return pmab_update_content($content, $tag, $num_of_blocks, $p);
                        }


                        return $content;
                    },
                    0
                );
                // do whatever you want with it
            }
        }
    }
}
