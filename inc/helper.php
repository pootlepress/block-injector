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
