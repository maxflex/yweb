<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Mail;
use App\Mail\NewOrder;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        Mail::to(config('mail.from.address'))->send(new NewOrder($request->all()));
    }
}
