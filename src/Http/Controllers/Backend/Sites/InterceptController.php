<?php

namespace System\Http\Controllers\Backend\Sites;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class InterceptController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => '拦截管理',
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
                    'name' => '拦截管理',
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.intercept.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.intercept.index'),
                ],
            ],
        ];

        $items = config('params.intercept');
        return view('backend::sites.intercept.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        settings([
            'params' => [
                'intercept' => array_replace_recursive(config('params.intercept'), [
                    'global_user' => $request->input('global_user'),
                    'email_verify' => $request->input('email_verify'),
                    'mobile_verify' => $request->input('mobile_verify'),
                ])
            ]
        ]);
        return redirect()->route('backend.sites.intercept.index')->with('message', trans('backend.messages.updated_success'));
    }
}
