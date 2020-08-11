<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use System\Models\Permission;
use System\Http\Requests\PermissionRequest;
use Illuminate\Http\Request;

class PermissionsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.permissions'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.permissions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.permissions.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.systems.permissions.create'),
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
                    'link' => route('backend.systems.permissions.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Permission::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('guard_name', 'like', $like);
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

        return view('backend::systems.permissions.index', compact('items', 'pages'));
    }

    public function show(Permission $permission)
    {
        $permission or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.permission')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.permissions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.permissions.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.permission')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.systems.permissions.show', $permission->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.permission')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.systems.permissions.edit', $permission->id),
                ],
            ],
        ];
        return view('backend::systems.permissions.show', compact('permission', 'pages'));
    }

    public function create(Permission $permission)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.permission')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.permissions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.permissions.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.permission')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.systems.permissions.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::systems.permissions.create_and_edit', compact('pages', 'permission'));
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create($request->all());
        return redirect()->route('backend.systems.permissions.show', $permission->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Permission $permission)
    {
        $permission or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.permission')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.permissions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.permissions.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.permission')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.systems.permissions.edit', $permission->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.systems.permissions.show', $permission->id),
                ],
            ],
        ];
        return view('backend::systems.permissions.create_and_edit', compact('permission', 'pages'));
    }

    public function update(PermissionRequest $request, Permission $permission)
    {
        $permission or abort(404);
        $permission->update($request->all());
        return redirect()->route('backend.systems.permissions.show', $permission->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            Permission::destroy(explode(',', $id));
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        Permission::find($id)->delete() or abort(404);
        return redirect()->route('backend.systems.permissions.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
