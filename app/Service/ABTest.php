<?php

namespace App\Service;

use App\Models\Variable;

class ABTest
{
    public static function parse($key, $ids)
    {
        $key = md5('abtest-' . $key);
        $ids = explode(',' , $ids);

        if (isset($_COOKIE[$key])) {
            $variant = $_COOKIE[$key];
        } else {
            $variant = mt_rand(0, count($ids) - 1);
            setcookie($key, $variant, time() + (10 * 365 * 24 * 60 * 60), '/');
        }
        return Variable::find($ids[$variant])->html;
    }
}
