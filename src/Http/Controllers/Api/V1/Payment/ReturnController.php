<?php

namespace System\Http\Controllers\Api\V1\Payment;

use System\Http\Controllers\Api\Controller;
use Illuminate\Http\Request;

class ReturnController extends Controller
{
    public function index($gateway, Request $request)
    {
        return $request->all();
    }
}
