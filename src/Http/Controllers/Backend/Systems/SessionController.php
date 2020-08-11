<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.database')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.system'),
                    'icon' => 'fa fa-gears',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.database')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.databases.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.databases.index'),
                ],
            ],
        ];

        $items = config('session');

        return view('backend::systems.session.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['session' => array_replace_recursive(config('session'), $items)]);
        return redirect()->route('backend.systems.session.index')->with('message', trans('backend.messages.update_success'));
    }
}
