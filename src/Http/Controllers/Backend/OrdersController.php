<?php

namespace System\Http\Controllers\Backend;

use System\Models\Order;
use System\Http\Controllers\Backend\Controller;
use System\Http\Requests\OrderRequest;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.orders'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.orders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.orders.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.orders.create'),
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
                    'link' => route('backend.orders.index'),
                ],
            ],
        ];

        $builder = Order::query()->with(['paymentLog', 'details']);

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('order_no', 'like', $like)
                    ->orWhere('shop_name', 'like', $like)
                    ->orWhere('consignee_name', 'like', $like)
                    ->orWhere('consignee_phone', 'like', $like)
                    ->orWhere('consignee_address', 'like', $like)
                    ->orWhere('remark', 'like', $like)
                    ->orWhereHas('details', function ($query) use ($like) {
                        $query->where('product_name', 'like', $like)
                            ->orWhere('product_sku_name', 'like', $like);
                    });
            });
        }

        // 订单号
        if ($request->filled('order_no')) {
            $builder->where('order_no', $request->input('order_no'));
        }

        // 支付单号
        if ($request->filled('payment_no')) {
            $payment_no = $request->input('payment_no');
            $builder->orWhereHas('paymentLog', function ($query) use ($payment_no) {
                $query->where('payment_no', $payment_no);
            });
        }

        // 状态
        if ($request->filled('status')) {
            $builder->where('status', $request->input('status'));
        }

        // 排序
        $builder->orderBy($request->input('order_by_column', 'id'), $request->input('order_by_direction', 'desc'));

        // 分页
        $items = $builder->paginate($request->input('per_page', config('params.pages.per_page', 15)))->appends(request()->query());

        return view('backend::orders.index', compact('items', 'pages'));
    }

    public function show(Order $order)
    {
        $order or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.order')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.orders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.orders.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.order')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.orders.show', $order->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.orders.create'),
                ], [
                    'name' => trans('backend.commons.edit'),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.orders.edit', $order->id),
                ],
            ],
        ];
        return view('backend::orders.show', compact('order', 'pages'));
    }

    public function create(Order $order)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.order')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.orders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.orders.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.order')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.orders.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::orders.create_and_edit', compact('pages', 'order'));
    }

    public function store(OrderRequest $request)
    {
        $order = Order::create($request->all());
        return redirect()->route('backend.orders.show', $order->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Order $order)
    {
        $order or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.order')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.orders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.orders.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.order')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.orders.edit', $order->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.orders.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.orders.show', $order->id),
                ],
            ],
        ];
        return view('backend::orders.create_and_edit', compact('order', 'pages'));
    }

    public function update(OrderRequest $request, Order $order)
    {
        $order or abort(404);
        $order->update($request->all());
        return redirect()->route('backend.orders.show', $order->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Order::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.orders.index')->with('message', trans('backend.messages.deleted_success'));
    }

    /*
     * 发货
     */
    public function shipping(Request $request, Order $order)
    {
        $order or abort(404);
        $express_name = $request->input('express_name');
        $express_no = $request->input('express_no');
        $order->update([
            'express_name' => $express_name,
            'express_no' => $express_no,
            'shipped_at' => now(),
            'status' => Order::STATUS_SHIPPED,
        ]);
        return redirect()->back()->with('message', '订单发货成功！');
    }
}
