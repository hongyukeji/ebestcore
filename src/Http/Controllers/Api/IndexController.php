<?php

namespace System\Http\Controllers\Api;

class IndexController extends Controller
{
    public function index()
    {
        $respond = [
            'status' => "0",
            'msg' => "Success",
            'data' => [
                'name' => config('app.name'),
                'version' => config('app.version'),
                'system_name' => config('app.system_name'),
                'author' => config('app.author'),
                'support' => config('app.support'),
            ],
        ];
        return response()->json($respond, 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }
}
