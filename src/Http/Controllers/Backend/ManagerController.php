<?php

namespace System\Http\Controllers\Backend;

use System\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use System\Http\Requests\AdminRequest;

class ManagerController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => '个人信息',
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => '个人信息',
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.manager.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit'),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.manager.create'),
                ], [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'id' => '',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.manager.index'),
                ],
            ],
        ];
        $id = auth('admin')->id();
        $admin = Admin::query()->findOrFail($id);
        return view('backend::manager.index', compact('pages', 'admin'));
    }

    public function create()
    {
        $pages = [
            'title' => '修改信息',
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => '个人信息',
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.manager.index'),
                ], [
                    'name' => '修改信息',
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.manager.create'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'id' => '',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.manager.create'),
                ],
            ],
        ];
        $id = auth('admin')->id();
        $admin = Admin::query()->findOrFail($id);
        return view('backend::manager.create', compact('pages', 'admin'));
    }

    public function store(Request $request)
    {
        $id = auth('admin')->id();
        $admin = Admin::query()->findOrFail($id);
        $request->validate([
            'name' => 'sometimes|string|nullable|max:255|unique:admins,name,' . $id,
            'mobile' => 'sometimes|mobile|nullable|max:255|unique:admins,mobile,' . $id,
            'email' => 'sometimes|email|nullable|max:255|unique:admins,email,' . $id,
            //'avatar' => 'sometimes|string|nullable|max:255',
            'password' => 'sometimes|string|nullable|min:6|max:255',
            'status' => 'sometimes|integer|nullable',
        ], [], [
            'name' => '用户名',
            'mobile' => '手机号',
            'email' => '邮箱',
        ]);
        $admin->update($request->all());
        return redirect()->route('backend.manager.index')->with('message', trans('backend.messages.updated_success'));
    }
}
