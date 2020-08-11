<?php

namespace System\Http\Controllers\Backend\Settings;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class GeoIpController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.ip_query')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.global'),
                    'icon' => 'fa fa-cog',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.ip_query')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.settings.geoip.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.settings.geoip.index'),
                ],
            ],
        ];

        $items = config('geoip');
        return view('backend::settings.geoip.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['geoip' => $items]);
        return redirect()->route('backend.settings.geoip.index')->with('message', trans('backend.messages.update_success'));
    }
}
