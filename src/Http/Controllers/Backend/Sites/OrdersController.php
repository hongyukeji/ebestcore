<?php

namespace System\Http\Controllers\Backend\Sites;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class OrdersController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.order')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.order')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.orders.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => '刷新',
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.orders.index'),
                ],
            ],
        ];

        $items = config('params.orders');
        return view('backend::sites.orders.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        settings([
            'params' => ['orders' => array_replace_recursive(config('params.orders'), $request->except('_token'))]
        ]);
        return redirect()->route('backend.sites.orders.index')->with('message', trans('backend.messages.update_success'));
    }
}
