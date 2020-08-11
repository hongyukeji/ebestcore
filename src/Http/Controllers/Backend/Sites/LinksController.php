<?php

namespace System\Http\Controllers\Backend\Sites;

use System\Http\Controllers\Backend\Controller;
use System\Models\Model;
use System\Models\Link;
use System\Http\Requests\LinkRequest;
use Illuminate\Http\Request;

class LinksController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.links'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.links'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.links.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.sites.links.create'),
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
                    'link' => route('backend.sites.links.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Link::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('link', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($request->filled('group')) {
            $builder->where('group', $request->input('group'));
        }

        if ($request->filled('status')) {
            $builder->where('status', $request->input('status'));
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

        return view('backend::sites.links.index', compact('items', 'pages'));
    }

    public function show(Link $link)
    {
        $link or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.link')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.links'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.links.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.link')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.sites.links.show', $link->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.link')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.sites.links.edit', $link->id),
                ],
            ],
        ];
        return view('backend::sites.links.show', compact('link', 'pages'));
    }

    public function create(Link $link)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.link')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.links'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.links.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.link')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.sites.links.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::sites.links.create_and_edit', compact('pages', 'link'));
    }

    public function store(LinkRequest $request)
    {
        $link = Link::create($request->all());
        return redirect()->route('backend.sites.links.show', $link->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Link $link)
    {
        $link or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.link')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.links'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.links.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.link')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.sites.links.edit', $link->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.sites.links.show', $link->id),
                ],
            ],
        ];
        return view('backend::sites.links.create_and_edit', compact('link', 'pages'));
    }

    public function update(LinkRequest $request, Link $link)
    {
        $link or abort(404);
        $link->update($request->all());
        return redirect()->route('backend.sites.links.show', $link->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Link::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.sites.links.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
