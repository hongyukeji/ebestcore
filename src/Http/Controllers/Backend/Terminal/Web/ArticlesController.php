<?php

namespace System\Http\Controllers\Backend\Terminal\Web;

use System\Http\Controllers\Backend\Controller;
use System\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.article')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.article')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.terminal.web.articles.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => '刷新',
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.terminal.web.articles.index'),
                ],
            ],
        ];

        $article_categories = ArticleCategory::query()->get();
        $items = config('terminal.web.articles');
        return view('backend::terminal.web.articles.index', compact('items', 'article_categories', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['terminal' => [
            'web' => [
                'articles' => array_replace_recursive(config('terminal.web.articles'), $items)
            ],
        ]]);
        return redirect()->route('backend.terminal.web.articles.index')->with('message', trans('backend.messages.update_success'));
    }
}
