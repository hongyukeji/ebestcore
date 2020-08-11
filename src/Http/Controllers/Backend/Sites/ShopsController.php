<?php

namespace System\Http\Controllers\Backend\Sites;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class ShopsController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.shop')]),
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.website'),
                    'icon' => 'fa fa-sitemap',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.shop')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.shops.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => '刷新',
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.shops.index'),
                ],
            ],
        ];

        $items = config('shop');
        return view('backend::sites.shops.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        settings(['shop' => array_replace_recursive(config('shop'), $request->except('_token'))]);
        return redirect()->route('backend.sites.shops.index')->with('message', trans('backend.messages.update_success'));
    }
}
