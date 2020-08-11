<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.security')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.security')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.security.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.security.index'),
                ],
            ],
        ];

        $items = config('systems.security');

        return view('backend::systems.security.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'administrator' => 'required|regex:/^[A-Za-z]+$/',
        ], [], [
            'administrator' => trans('backend.commons.administrator'),
        ]);

        $items = $request->except('_token');

        settings(['systems' => ['security' => $items]]);
        return redirect()->route('backend.systems.security.index')->with('message', trans('backend.messages.update_success'));
    }
}
