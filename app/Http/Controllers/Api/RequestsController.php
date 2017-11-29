<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Service\Api;
use App\Http\Requests\RequestStore;
use App\Service\Limiter;
use Illuminate\Support\Facades\Redis;
use DB;

class RequestsController extends Controller
{
    public function store(RequestStore $request)
    {
        DB::table('request_log')->insert([
            'data' => json_encode($request->all())
        ]);
        return Limiter::run('request', 24, 200, function() use ($request) {
            Api::exec('AddRequest', array_merge($request->input(), [
                'branches' => [$request->branch_id]
            ]));
        }, function() use ($request) {
            Redis::sadd('ecweb:request:blocked', json_encode($request->input()));
        }, 'Внимание! DDoS на заявки');
    }
}
