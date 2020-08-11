<?php

namespace System\Http\Controllers\Backend;

use System\Http\Controllers\Backend\Controller;
use System\Models\User;
use System\Http\Requests\UserRequest;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.users'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.users'),
                    'icon' => 'fa fa-users',
                    'link' => route('backend.users.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.users.create'),
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
                    'link' => route('backend.users.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = User::query()->with(['account']);

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

        if ($request->filled('status')) {
            $status = $request->input('status');
            $builder->where('status', $status);
        }

        // 排序
        $sort_key = $request->input('order_by_column', 'id');
        $sort_value = $request->input('order_by_direction', 'desc');
        $filters['order_by_column'] = $sort_key;
        $filters['order_by_direction'] = $sort_value;
        $builder->orderBy($sort_key, $sort_value);

        if (request()->ajax()) {
            $data = $builder->get();
            return response()->json(api_result(0, null, $data), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page'));
        $filters['per_page'] = $per_page;
        $items = $builder->paginate($per_page)->appends($filters);

        return view('backend::users.index', compact('items', 'pages'));
    }

    public function show(User $user)
    {
        $user or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.user')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.users'),
                    'icon' => 'fa fa-users',
                    'link' => route('backend.users.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.user')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.users.show', $user->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.users.edit', $user->id),
                ],
            ],
        ];
        return view('backend::users.show', compact('user', 'pages'));
    }

    public function create(User $user)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.user')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.users'),
                    'icon' => 'fa fa-users',
                    'link' => route('backend.users.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.user')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.users.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::users.create_and_edit', compact('pages', 'user'));
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->all());
        return redirect()->route('backend.users.show', $user->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(User $user)
    {
        $user or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.users'),
                    'icon' => 'fa fa-users',
                    'link' => route('backend.users.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.users.edit', $user->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.users.show', $user->id),
                ],
            ],
        ];
        return view('backend::users.create_and_edit', compact('user', 'pages'));
    }

    public function update(UserRequest $request, User $user)
    {
        $user or abort(404);
        $user->update($request->all());
        return redirect()->route('backend.users.show', $user->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        User::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.users.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
