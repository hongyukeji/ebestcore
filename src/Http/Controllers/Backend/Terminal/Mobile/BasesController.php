<?php

namespace System\Http\Controllers\Backend\Terminal\Mobile;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class BasesController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.base')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.terminal'),
                    'icon' => 'fa fa-gears',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.mobile_end'),
                    'icon' => 'fa fa-mobile',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.basic')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.terminal.mobile.bases.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.terminal.mobile.bases.index'),
                ],
            ],
        ];
        $items = config('terminal.mobile.bases');
        return view('backend::terminal.mobile.bases.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['terminal' => ['mobile' => ['bases' => array_replace_recursive(config('terminal.mobile.bases'), $items)]]]);
        return redirect()->route('backend.terminal.mobile.bases.index')->with('message', trans('backend.messages.update_success'));
    }
}
