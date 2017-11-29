<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

class StreamController extends Controller
{
    public function store(Request $request)
    {
        $ip = @$_SERVER['HTTP_X_REAL_IP'];

        if (strpos($ip, '213.184.130.') === 0 || $ip == '77.37.220.250' || isTestSubdomain()) {
            return;
        }

        $request->merge([
            'mobile' => isMobile(),
            'ip' => $ip,
        ]);
        
        egecrm('stream')->insert($request->all());
    }
}
