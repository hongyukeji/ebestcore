<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Requests\AdminRequest;
use System\Models\Admin;
use System\Models\Role;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Gate;

class AdminsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.admins'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.admins'),
                    'icon' => 'fa fa-admins',
                    'link' => route('backend.systems.admins.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.systems.admins.create'),
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
                    'link' => route('backend.systems.admins.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Admin::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('mobile', 'like', $like)
                    ->orWhere('email', 'like', $like);
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

        return view('backend::systems.admins.index', compact('items', 'pages'));
    }

    public function show(Admin $admin)
    {
        $admin or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.admin')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.admins'),
                    'icon' => 'fa fa-admins',
                    'link' => route('backend.systems.admins.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.admin')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.systems.admins.show', $admin->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.admin')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.systems.admins.edit', $admin->id),
                ],
            ],
        ];
        return view('backend::systems.admins.show', compact('admin', 'pages'));
    }

    public function create(Admin $admin)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.admin')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.admins'),
                    'icon' => 'fa fa-admins',
                    'link' => route('backend.systems.admins.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.admin')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.systems.admins.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::systems.admins.create_and_edit', compact('pages', 'admin'));
    }

    public function store(AdminRequest $request)
    {
        $admin = Admin::create($request->all());
        return redirect()->route('backend.systems.admins.show', $admin->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Admin $admin)
    {
        $admin or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.admin')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.admins'),
                    'icon' => 'fa fa-admins',
                    'link' => route('backend.systems.admins.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.admin')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.systems.admins.edit', $admin->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.systems.admins.show', $admin->id),
                ],
            ],
        ];
        return view('backend::systems.admins.create_and_edit', compact('admin', 'pages'));
    }

    public function update(AdminRequest $request, Admin $admin)
    {
        //$this->authorizeForUser(auth('admin')->user(), 'super_admin');

        // 判断是否是超级管理员
        /*if (!Gate::forUser(auth('admin')->user())->allows('super_admin')) {
            throw new \System\Exceptions\UnauthorizedException(trans('backend.messages.not_have_handle_permission'), '403');
        }*/

        $admin or abort(404);
        $admin->update($request->all());
        return redirect()->route('backend.systems.admins.show', $admin->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        if (request()->ajax()) {
            Admin::destroy(explode(',', $id));
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        Admin::find($id)->delete() or abort(404);
        return redirect()->route('backend.systems.admins.index')->with('message', trans('backend.messages.deleted_success'));
    }

    public function showRolesForm(Admin $admin, Role $role)
    {
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.admin')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.admins'),
                    'icon' => 'fa fa-admins',
                    'link' => route('backend.systems.admins.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.admin')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.systems.admins.edit', $admin->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.systems.admins.show', $admin->id),
                ],
            ],
        ];
        $roles = $role->getGroupList();
        return view('backend::systems.admins.roles', compact('admin', 'roles', 'pages'));
    }

    public function roles(Request $request, Admin $admin)
    {
        $admin->syncRoles($request->name);
        return redirect()->route('backend.systems.admins.index')->with('success', trans('backend.commons.role') . trans('backend.commons.setting_success'));
    }
}
