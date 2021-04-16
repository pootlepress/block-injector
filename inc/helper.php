<?php
if (!function_exists('selected')) {
    function selected($val, $val2)
    {
        return $val === $val2 ? 'selected=selected' : '';
    }
}
