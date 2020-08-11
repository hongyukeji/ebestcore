<?php

namespace System\Http\Controllers\Backend;

use System\Models\Page;
use System\Http\Controllers\Backend\Controller;
use System\Http\Requests\PageRequest;
use Illuminate\Http\Request;

class PagesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.pages'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.pages'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.pages.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.pages.create'),
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
                    'link' => route('backend.pages.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Page::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
            $search = "%{$filters['search']}%";
            $builder->where(function ($query) use ($search) {
                $query->where('title', 'like', $search)
                    ->orWhere('slug', 'like', $search)
                    ->orWhere('keywords', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $filters['status'] = $request->input('status');
            $builder->where('status', $filters['status']);
        }

        // 排序
        $filters['order_by_column'] = $request->input('order_by_column', 'id');
        $filters['order_by_direction'] = $request->input('order_by_direction', 'desc');
        $builder->orderBy($filters['order_by_column'], $filters['order_by_direction']);

        // 分页
        $filters['per_page'] = $request->input('per_page', config('params.pages.per_page', 15));
        $items = $builder->paginate($filters['per_page'])->appends($filters);

        return view('backend::pages.index', compact('items', 'pages'));
    }

    public function show(Page $page)
    {
        $page or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.page')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.pages'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.pages.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.page')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.pages.show', $page->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.pages.create'),
                ], [
                    'name' => trans('backend.commons.edit'),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.pages.edit', $page->id),
                ],
            ],
        ];
        return view('backend::pages.create_and_edit_show', compact('page', 'pages'));
    }

    public function create(Page $page)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.page')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.pages'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.pages.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.page')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.pages.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::pages.create_and_edit_show', compact('pages', 'page'));
    }

    public function store(PageRequest $request)
    {
        $page = Page::create($request->all());
        return redirect()->route('backend.pages.show', $page->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Page $page)
    {
        $page or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.page')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.pages'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.pages.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.page')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.pages.edit', $page->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.pages.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.pages.show', $page->id),
                ],
            ],
        ];
        return view('backend::pages.create_and_edit_show', compact('page', 'pages'));
    }

    public function update(PageRequest $request, Page $page)
    {
        $page or abort(404);
        $page->update($request->all());
        return redirect()->route('backend.pages.show', $page->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Page::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.pages.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
