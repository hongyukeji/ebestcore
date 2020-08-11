<?php

namespace System\Http\Controllers\Backend\Terminal\Api;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.article')]),
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
                    'name' => trans('backend.commons.api_end'),
                    'icon' => 'fa fa-globe',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.article')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.terminal.api.articles.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.terminal.api.articles.index'),
                ],
            ],
        ];
        $items = config('terminal.api.articles');
        return view('backend::terminal.api.articles.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['terminal' => ['api' => ['articles' => array_replace_recursive(config('terminal.api.articles'), $items)]]]);
        return redirect()->route('backend.terminal.api.articles.index')->with('message', trans('backend.messages.update_success'));
    }
}
