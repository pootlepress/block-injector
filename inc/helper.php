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

        // loop over each post
        foreach (get_posts(
            array('post_type'      => 'block_injector', 'post_status'    => 'publish', 'posts_per_page' => -1)
        ) as $p) {


            // get the meta you need form each post_pmab_meta_specific_post
            $num_of_blocks        = get_post_meta($p->ID, '_pmab_meta_number_of_blocks', true);
            $tag_type             = get_post_meta($p->ID, '_pmab_meta_tag_n_fix', true);
            $startdate            = get_post_meta($p->ID, '_pmab_meta_startdate', true);
            $expiredate           = get_post_meta($p->ID, '_pmab_meta_expiredate', true);
            $dateandtime = pmab_expire_checker($startdate, $expiredate);

            $tag_type = is_string($tag_type) ? explode('_', $tag_type) : array();

            if (!empty($tag_type) && isset($tag_type[0]) && $dateandtime) {
                $tag          = $tag_type[0];
                switch ($tag) {
                    case 'top':
                        $num_of_blocks = 0;
                        break;
                    case "bottom":
                        $num_of_blocks = PHP_INT_MAX;
                        break;
                }
                add_filter('the_content', function ($content) use ($p, $tag, $num_of_blocks) {
                    return pmab_filter_hook($content, $p, $tag, $num_of_blocks);
                }, 0);
            }
        }
    }
}

if (!function_exists('pmab_filter_hook')) {
    function pmab_filter_hook($content, $p, $tag, $num_of_blocks)
    {

        $inject_content_type  = get_post_meta($p->ID, '_pmab_meta_type', true); //
        $inject_content_type2 = get_post_meta($p->ID, '_pmab_meta_type2', true); //
        $specific_post        = get_post_meta($p->ID, '_pmab_meta_specific_post', true);
        $specific_post_exclude = get_post_meta($p->ID, '_pmab_meta_specific_post_exclude', true);
        $tags                 = get_post_meta($p->ID, '_pmab_meta_tags', true);
        $category             = get_post_meta($p->ID, '_pmab_meta_category', true);

        $tag_posts = $tags ? get_posts(array('posts_per_page' => -1, 'tag__in' => explode(',', $tags))) : array();
        $thisposts_exclude = is_string($specific_post_exclude) ? explode(',', $specific_post_exclude) : array();
        $thisposts = get_posts(array('posts_per_page' => -1, 'post__in' => $specific_post));
        // Check if we're inside the main loop in a single Post.
        switch ($inject_content_type) {
            case "tags":
                pmab_posts_filter_content($tag_posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, "is_single");

                break;
            case "category":
                if (is_single()) {
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
                break;
            case "post":
                pmab_posts_filter_content($thisposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, "is_single");
                break;
            case "page":
                pmab_posts_filter_content($thisposts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, "is_page");
                break;
            case "all_post":
                if (is_single()) {
                    pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'post_exclude', $content, $tag, $num_of_blocks, $p);
                }
                break;
            case "all_page":
                if (is_page()) {
                    pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'page_exclude', $content, $tag, $num_of_blocks, $p);
                }
                break;
            case "post_page":
                if (is_page() || is_single()) {
                    pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, 'both', $content, $tag, $num_of_blocks, $p);
                }
                break;
        }



        return $content;
    }
}



if (!function_exists('pmab_posts_filter_content')) {
    function pmab_posts_filter_content($posts, $thisposts_exclude, $inject_content_type2, $content, $tag, $num_of_blocks, $p, $function_name)
    {
        foreach ($posts as $post) {

            if ($function_name($post->ID)) {
                if ($inject_content_type2 == 'post_exclude' && !in_array($post->ID, $thisposts_exclude)) {
                    return pmab_update_content($content, $tag, $num_of_blocks, $p);
                }
                return pmab_update_content($content, $tag, $num_of_blocks, $p);
            }
        }
    }
}
if (!function_exists('pmab_filter_exclude_content')) {
    function pmab_filter_exclude_content($thisposts_exclude, $inject_content_type2, $exclude_type, $content, $tag, $num_of_blocks, $p)
    {
        if ($exclude_type == "both") {
            foreach (array('post_exclude', 'page_exclude') as $exclude) {
                if ($inject_content_type2 == $exclude && !in_array(get_post()->ID, $thisposts_exclude)) {
                    return pmab_update_content($content, $tag, $num_of_blocks, $p);
                }
                return pmab_update_content($content, $tag, $num_of_blocks, $p);
            }
            return;
        }
        if ($inject_content_type2 == $exclude_type && !in_array(get_post()->ID, $thisposts_exclude)) {
            return pmab_update_content($content, $tag, $num_of_blocks, $p);
        }
        return pmab_update_content($content, $tag, $num_of_blocks, $p);
    }
}
