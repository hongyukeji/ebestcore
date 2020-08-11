<?php

namespace System\Http\Controllers\Backend\Sites;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => '注册管理',
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
                    'name' => '注册管理',
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.register.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.register.index'),
                ],
            ],
        ];

        $items = config('params.register');
        return view('backend::sites.register.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        settings([
            'params' => [
                'register' => array_replace_recursive(config('params.register'), $request->except('_token'))
            ]
        ]);
        return redirect()->route('backend.sites.register.index')->with('message', trans('backend.messages.updated_success'));
    }
}
