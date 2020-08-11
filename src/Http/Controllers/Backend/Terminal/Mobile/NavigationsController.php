<?php

namespace System\Http\Controllers\Backend\Terminal\Mobile;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class NavigationsController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.navigation')]),
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
                    'icon' => 'fa fa-globe',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.navigation')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.terminal.mobile.navigations.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.terminal.mobile.navigations.index'),
                ],
            ],
        ];
        $items = config('terminal.mobile.navigations');
        return view('backend::terminal.mobile.navigations.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['terminal' => ['mobile' => ['navigations' => array_replace_recursive(config('terminal.mobile.navigations'), $items)]]]);
        return redirect()->route('backend.terminal.mobile.navigations.index')->with('message', trans('backend.messages.update_success'));
    }
}
