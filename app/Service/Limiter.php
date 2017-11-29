<?php

namespace App\Service;
use Illuminate\Support\Facades\Redis;
use App\Models\Service\Sms;
/**
 * Ограничить обращения
 */
class Limiter
{
    public static function run($key, $hours, $max, $success, $fail = null, $sms_text = null)
    {
        $key = "ecweb:{$key}:count";
        $count = intval(Redis::get($key));
        Redis::incr($key);
        if ($count < $max) {
            $return = $success();
            if ($count == 0) {
                Redis::expire($key, 3600 * $hours);
            }
            return $return;
        } else {
            if (is_callable($fail)) {
                $fail();
            }
            return abort(403);
        }
    }
}
