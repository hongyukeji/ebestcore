<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use System\Models\Permission;
use System\Models\Role;
use System\Http\Requests\RoleRequest;
use Illuminate\Http\Request;

class RolesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.roles'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.roles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.roles.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.systems.roles.create'),
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
                    'link' => route('backend.systems.roles.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Role::query();

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

        return view('backend::systems.roles.index', compact('items', 'pages'));
    }

    public function show(Role $role)
    {
        $role or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.role')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.roles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.roles.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.role')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.systems.roles.show', $role->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.role')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.systems.roles.edit', $role->id),
                ],
            ],
        ];
        return view('backend::systems.roles.show', compact('role', 'pages'));
    }

    public function create(Role $role)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.role')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.roles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.roles.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.role')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.systems.roles.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::systems.roles.create_and_edit', compact('pages', 'role'));
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create($request->all());
        return redirect()->route('backend.systems.roles.show', $role->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Role $role)
    {
        $role or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.role')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.roles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.roles.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.role')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.systems.roles.edit', $role->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.systems.roles.show', $role->id),
                ],
            ],
        ];
        return view('backend::systems.roles.create_and_edit', compact('role', 'pages'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $role or abort(404);
        $role->update($request->all());
        return redirect()->route('backend.systems.roles.show', $role->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            Role::destroy(explode(',', $id));
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        Role::find($id)->delete() or abort(404);
        return redirect()->route('backend.systems.roles.index')->with('message', trans('backend.messages.deleted_success'));
    }

    public function showPermissionsForm(Role $role, Permission $permission)
    {
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.role')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.roles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.roles.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.role')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.systems.roles.edit', $role->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.systems.roles.show', $role->id),
                ],
            ],
        ];
        $permissions = $permission->getGroupList();
        return view('backend::systems..roles.permissions', compact('role', 'permissions', 'pages'));
    }

    public function permission(Request $request, Role $role)
    {
        $role->syncPermissions($request->name);
        return redirect()->route('backend.systems.roles.index')->with('success', trans('backend.commons.permission') . trans('backend.commons.setting_success'));
    }

}
