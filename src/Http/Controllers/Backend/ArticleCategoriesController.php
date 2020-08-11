<?php

namespace System\Http\Controllers\Backend;

use System\Models\ArticleCategory;
use System\Http\Requests\ArticleCategoryRequest;
use Illuminate\Http\Request;

class ArticleCategoriesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.article_categories'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.article_categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.article-categories.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.article-categories.create'),
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
                    'link' => route('backend.article-categories.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = ArticleCategory::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
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

        return view('backend::article-categories.index', compact('items', 'pages'));
    }

    public function show(ArticleCategory $articleCategory)
    {
        $articleCategory or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.article_category')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.article_categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.article-categories.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.article_category')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.article-categories.show', $articleCategory->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.article-categories.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.article_category')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.article-categories.edit', $articleCategory->id),
                ],
            ],
        ];
        return view('backend::article-categories.show', compact('articleCategory', 'pages'));
    }

    public function create(ArticleCategory $articleCategory)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.article_category')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.article_categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.article-categories.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.article_category')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.article-categories.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::article-categories.create_and_edit', compact('pages', 'articleCategory'));
    }

    public function store(ArticleCategoryRequest $request)
    {
        $articleCategory = ArticleCategory::create($request->all());
        return redirect()->route('backend.article-categories.show', $articleCategory->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(ArticleCategory $articleCategory)
    {
        $articleCategory or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.article_category')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.article_categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.article-categories.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.article_category')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.article-categories.edit', $articleCategory->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.article-categories.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.article-categories.show', $articleCategory->id),
                ],
            ],
        ];
        return view('backend::article-categories.create_and_edit', compact('articleCategory', 'pages'));
    }

    public function update(ArticleCategoryRequest $request, ArticleCategory $articleCategory)
    {
        $articleCategory or abort(404);
        $articleCategory->update($request->all());
        return redirect()->route('backend.article-categories.show', $articleCategory->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        ArticleCategory::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.article-categories.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
