<?php

namespace System\Http\Controllers\Backend\User;

use System\Models\UserAccount;
use System\Http\Requests\UserAccountRequest;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class AccountsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.user_account'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.member_center'),
                    'icon' => 'fa fa-user',
                    'link' => route('backend.users.index'),
                ], [
                    'name' => trans('backend.commons.user_account'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.accounts.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.user.accounts.create'),
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
                    'link' => route('backend.user.accounts.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = UserAccount::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->orWhereHas('user', function ($query) use ($like) {
                    $query->where('name', 'like', '%' . $like . '%')
                        ->orWhere('email', 'like', $like)
                        ->orWhere('mobile', 'like', $like);
                });
            });
        }

        // 排序
        $sort_key = $request->input('order_by_column', 'id');
        $sort_value = $request->input('order_by_direction', 'desc');
        $filters['order_by_column'] = $sort_key;
        $filters['order_by_direction'] = $sort_value;
        $builder->orderBy($sort_key, $sort_value);

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page'));
        $filters['per_page'] = $per_page;
        $items = $builder->paginate($per_page)->appends($filters);

        return view('backend::user.accounts.index', compact('items', 'pages'));
    }

    public function show(UserAccount $account)
    {
        $userAccount = $account;
        $userAccount or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.user_account')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.user_account'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.accounts.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.user_account')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.user.accounts.show', $userAccount->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user_account')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.user.accounts.edit', $userAccount->id),
                ],
            ],
        ];
        return view('backend::user.accounts.show', compact('userAccount', 'pages'));
    }

    public function create(UserAccount $userAccount)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.user_account')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.user_account'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.accounts.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.user_account')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.user.accounts.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::user.accounts.create_and_edit', compact('pages', 'userAccount'));
    }

    public function store(UserAccountRequest $request)
    {
        $userAccount = UserAccount::create($request->all());
        return redirect()->route('backend.user.accounts.show', $userAccount->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(UserAccount $account)
    {
        $userAccount = $account;
        $userAccount or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user_account')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.user_account'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.accounts.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user_account')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.user.accounts.edit', $userAccount->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.user.accounts.show', $userAccount->id),
                ],
            ],
        ];
        return view('backend::user.accounts.create_and_edit', compact('userAccount', 'pages'));
    }

    public function update(UserAccountRequest $request, UserAccount $account)
    {
        $userAccount = $account;
        $userAccount or abort(404);
        $userAccount->update($request->all());
        if (request()->ajax()) {
            return response()->json(null, 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->back()->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        UserAccount::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.users.users.user-accounts.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
