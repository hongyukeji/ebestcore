<?php

namespace System\Http\Controllers\Backend;

use System\Models\Article;
use System\Http\Requests\ArticleRequest;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.articles'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.articles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.articles.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.articles.create'),
                ], [
                    'name' => trans('backend.commons.delete'),
                    'icon' => 'fa fa-trash-o',
                    'class' => 'btn btn-danger ajax-delete',
                    'link' => 'javascript:;',
                ], [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'id' => '',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.articles.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Article::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        // 排序
        $sort_key = $request->input('order_by_column', 'created_at');
        $sort_value = $request->input('order_by_direction', 'desc');
        $filters['order_by_column'] = $sort_key;
        $filters['order_by_direction'] = $sort_value;
        $builder->orderBy($sort_key, $sort_value);

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page'));
        $filters['per_page'] = $per_page;
        $items = $builder->paginate($per_page)->appends($filters);

        return view('backend::articles.index', compact('items', 'pages'));
    }

    public function show(Article $article)
    {
        $article or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.article')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.articles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.articles.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.article')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.articles.show', $article->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.articles.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.article')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.articles.edit', $article->id),
                ],
            ],
        ];
        return view('backend::articles.show', compact('article', 'pages'));
    }

    public function create(Article $article)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.article')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.articles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.articles.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.article')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.articles.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::articles.create_and_edit', compact('pages', 'article'));
    }

    public function store(ArticleRequest $request)
    {
        $article = Article::create($request->all());
        return redirect()->route('backend.articles.show', $article->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Article $article)
    {
        $article or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.article')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.articles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.articles.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.article')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.articles.edit', $article->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.articles.show', $article->id),
                ],
            ],
        ];
        return view('backend::articles.create_and_edit', compact('article', 'pages'));
    }

    public function update(ArticleRequest $request, Article $article)
    {
        $article or abort(404);
        $article->update($request->all());
        return redirect()->route('backend.articles.show', $article->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Article::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.articles.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
