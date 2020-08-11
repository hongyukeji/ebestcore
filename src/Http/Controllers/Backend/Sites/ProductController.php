<?php

namespace System\Http\Controllers\Backend\Sites;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => '商品设置',
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
                    'name' => '商品设置',
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.product.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.product.index'),
                ],
            ],
        ];

        $items = config('params.products');
        return view('backend::sites.product.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings([
            'params' => [
                'products' => array_replace_recursive(config('params.products'), $items)
            ]
        ]);
        return redirect()->route('backend.sites.product.index')->with('message', trans('backend.messages.updated_success'));
    }
}
