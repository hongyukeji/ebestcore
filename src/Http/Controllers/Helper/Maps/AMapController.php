<?php

namespace System\Http\Controllers\Helper\Maps;

use System\Http\Controllers\Helper\Controller;
use Illuminate\Http\Request;

class AMapController extends Controller
{
    public function index()
    {
        //
    }

    /*
     * $location [116.39564504,39.92998578] 经度,维度
     */
    public function show($location, Request $request)
    {
        // TODO: Debugbar 调试视图隐藏
        if (class_exists('\Debugbar')) {
            \Debugbar::disable();
        }

        $locations = explode(",", $location);

        $zoom = $request->input('zoom', '18');
        $lng = $locations[0] ?? 116.4071700;   // 经度
        $lat = $locations[1] ?? 39.9046900;   // 维度
        return view('helper.maps.amap.show', compact('lng', 'lat', 'zoom'));
    }
}
