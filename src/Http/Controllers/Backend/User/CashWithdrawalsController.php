<?php

namespace System\Http\Controllers\Backend\User;

use System\Models\CashWithdrawal;
use System\Http\Requests\CashWithdrawalRequest;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class CashWithdrawalsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.cash_withdrawal'),
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
                    'name' => trans('backend.commons.cash_withdrawal'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.cash-withdrawals.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.user.cash-withdrawals.create'),
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
                    'link' => route('backend.user.cash-withdrawals.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = CashWithdrawal::query();

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

        return view('backend::user.cash-withdrawals.index', compact('items', 'pages'));
    }

    public function show(CashWithdrawal $cashWithdrawal)
    {
        $cashWithdrawal or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.cash_withdrawal')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.cash_withdrawal'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.cash-withdrawals.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.cash_withdrawal')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.user.cash-withdrawals.show', $cashWithdrawal->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.cash_withdrawal')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.user.cash-withdrawals.edit', $cashWithdrawal->id),
                ],
            ],
        ];
        return view('backend::user.cash-withdrawals.show', compact('cashWithdrawal', 'pages'));
    }

    public function create(CashWithdrawal $cashWithdrawal)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.cash_withdrawal')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.cash_withdrawal'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.cash-withdrawals.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.cash_withdrawal')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.user.cash-withdrawals.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::user.cash-withdrawals.create_and_edit', compact('pages', 'cashWithdrawal'));
    }

    public function store(CashWithdrawalRequest $request)
    {
        $cashWithdrawal = CashWithdrawal::create($request->all());
        return redirect()->route('backend.user.cash-withdrawals.show', $cashWithdrawal->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(CashWithdrawal $cashWithdrawal)
    {
        $cashWithdrawal or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.cash_withdrawal')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.cash_withdrawal'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.cash-withdrawals.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.cash_withdrawal')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.user.cash-withdrawals.edit', $cashWithdrawal->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.user.cash-withdrawals.show', $cashWithdrawal->id),
                ],
            ],
        ];
        return view('backend::user.cash-withdrawals.create_and_edit', compact('cashWithdrawal', 'pages'));
    }

    public function update(CashWithdrawalRequest $request, CashWithdrawal $cashWithdrawal)
    {
        $cashWithdrawal or abort(404);
        $cashWithdrawal->update($request->all());
        if (request()->ajax()) {
            return response()->json(null, 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->back()->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        CashWithdrawal::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.users.users.user-accounts.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
