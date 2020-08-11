<?php

namespace System\Http\Controllers\Backend\Settings;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class OAuthController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.oauth')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.oauth')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.settings.oauth.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.settings.oauth.index'),
                ],
            ],
        ];

        $items = config('oauth');

        return view('backend::settings.oauth.index', compact('items', 'pages'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['oauth' => $items]);
        return redirect()->route('backend.settings.oauth.index')->with('message', trans('backend.messages.update_success'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
