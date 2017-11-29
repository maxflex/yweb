<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CvStore;
use App\Http\Controllers\Controller;
use App\Models\Service\Api;
use App\Service\Limiter;
use Illuminate\Support\Facades\Redis;

class CvController extends Controller
{
    public function store(CvStore $request)
    {
        // не более 200 за последние 24 часа
        return Limiter::run('cv', 24, 200, function() use ($request) {
            Api::exec('tutorNew', $request->input());
        }, function() use ($request) {
            Redis::sadd('ecweb:cv:blocked', json_encode($request->input()));
        }, 'Внимание! DDoS на анкеты');
    }
}
