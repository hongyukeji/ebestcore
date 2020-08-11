<?php

namespace System\Http\Controllers\Backend\Product;

use System\Models\Model;
use System\Models\Category;
use System\Http\Controllers\Backend\Controller;
use System\Http\Requests\CategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use System\Repository\Interfaces\CategoryInterface;

class CategoryController extends Controller
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }

    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.categories'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.categories.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.categories.create'),
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
                    'link' => route('backend.product.categories.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Category::query();

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

        if ($request->filled('status')) {
            $status = $request->input('status');
            $builder->where('status', $status);
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
        $categories = Category::query()->orderBy('sort', 'desc')->get([
            'id', 'name', 'parent_id', 'sort', DB::raw('name as text')
        ]);
        return view('backend::product.categories.index', compact('items', 'pages', 'categories'));
    }

    public function show(Category $category)
    {
        $category or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.category')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.categories.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.category')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.product.categories.show', $category->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.categories.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.category')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.product.categories.edit', $category->id),
                ],
            ],
        ];
        return view('backend::product.categories.show', compact('category', 'pages'));
    }

    public function create(Category $category)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.category')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.categories.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.category')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.product.categories.create'),
                    'active' => true,
                ],
            ],
        ];
        $categories = $this->category->findTrees();
        return view('backend::product.categories.create_and_edit', compact('pages', 'category', 'categories'));
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->all());
        return redirect()->route('backend.product.categories.show', $category->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Category $category)
    {
        $category or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.category')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.categories'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.categories.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.category')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.product.categories.edit', $category->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.categories.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.product.categories.show', $category->id),
                ],
            ],
        ];
        $categories = $this->category->findTrees();
        return view('backend::product.categories.create_and_edit', compact('category', 'categories', 'pages'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category or abort(404);
        $category->update($request->all());
        return redirect()->route('backend.product.categories.show', $category->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Category::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.product.categories.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
