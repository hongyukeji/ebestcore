<?php

namespace System\Repository\Repositories;

use System\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use System\Repository\Interfaces\OrderInterface;

class OrderRepository extends Repository implements OrderInterface
{
    public function search()
    {
        $request = request();
        $builder = Order::query()->with(['details']);

        // 用户ID
        if ($request->filled('user_id')) {
            $builder->where('user_id', $request->input('user_id'));
        }

        // 根据订单状态code查询
        if ($request->filled('order_status')) {
            $order_status = $request->input('order_status');
            switch ($order_status) {
                case "unpaid":  // 未付款
                    $builder->statusUnpaid();
                    break;
                case "wait_delivery":   // 待发货
                    $builder->statusWaitDelivery();
                    break;
                case "wait_received":  // 待收货
                    $builder->statusWaitReceived();
                    break;
                case "finish":  // 已完成
                    $builder->statusFinish();
                    break;
                case "wait_comment":  // 待评价
                    $builder->statusWaitComment();
                    break;
                default:
            }
        }

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('order_no', 'like', $like)
                    ->orWhere('shop_name', 'like', $like)
                    ->orWhere('express_no', 'like', $like)
                    ->orWhere('consignee_name', 'like', $like)
                    ->orWhere('consignee_phone', 'like', $like)
                    ->orWhere('consignee_address', 'like', $like)
                    ->orWhere('user_name', 'like', $like)
                    ->orWhere('remark', 'like', $like)
                    ->orWhereHas('shop', function ($query) use ($like) {
                        $query->where('name', 'like', '%' . $like . '%');
                    });
            });
        }

        // 订单号
        if ($request->filled('order_no')) {
            $builder->where('order_no', $request->input('order_no'));
        }

        if ($request->filled('user_id')) {
            $builder->where('user_id', $request->input('user_id'));
        }

        // 商品状态
        if ($request->filled('status')) {
            $builder->where('status', $request->input('status'));
        }

        // 排序
        $builder->orderBy($request->input('sort', 'created_at'), $request->input('order', 'desc'));

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page', 15));
        return $builder->paginate($per_page)->appends(request()->query());
    }

    public function queryTotalAmount($days = null, $sort = 'ASC')
    {
        if (is_null($days)) {
            $days = request('days', 7);
        }
        $range = Carbon::today()->subDays($days);
        $items = Order::where('created_at', '>=', $range)
            ->groupBy('date')
            ->orderBy('date', $sort)
            ->get([
                DB::raw('Date(created_at) as date'),
                DB::raw('sum(total_amount) AS total_amount'),
                DB::raw('sum(CASE WHEN id > 0 THEN 1 ELSE 0 END) AS total_amount_number'),
                DB::raw('sum(CASE WHEN `payment_at` is not null THEN total_amount ELSE 0 END) AS amount_paid'),
                DB::raw('sum(CASE WHEN `payment_at` is not null THEN 1 ELSE 0 END) AS amount_paid_number'),
            ]);
        return $items;
    }

    public function findAll()
    {
        return Order::all();
    }

    public function createCount($shop_id = null)
    {
        $order = Order::query();
        if ($shop_id) {
            $order->where('shop_id', $shop_id);
        }
        $data = [];
        // 今天数据
        $data['today_count'] = $order->whereDate('created_at', Carbon::today())->count();
        // 昨天数据
        $data['yesterday_count'] = $order->whereDate('created_at', Carbon::yesterday())->count();
        // 本周数据
        $this_week = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $data['this_week_count'] = $order->whereBetween('created_at', $this_week)->count();
        // 上周数据
        $last_week = [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()];
        $data['last_week_count'] = $order->whereBetween('created_at', $last_week)->count();
        // 本月数据
        $data['this_month_count'] = $order->whereMonth('created_at', Carbon::now()->month)->count();
        // 上月数据
        $data['last_month_count'] = $order->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        // 本年数据
        $data['this_year_count'] = $order->whereYear('created_at', Carbon::now()->year)->count();
        // 上一年
        $data['last_year_count'] = $order->whereYear('created_at', Carbon::now()->subYear()->year)->count();
        return $data;
    }

    public function findOne($id)
    {
        return Order::query()->find($id);
    }

    public function getDailyIncome($shop_id = null)
    {
        $order = Order::query();
        if ($shop_id) {
            $order->where('shop_id', $shop_id);
        }
        $list = $order
            ->select(
                DB::raw('Date(created_at) as date'),
                DB::raw('COUNT(id) as count'),
                DB::raw('SUM(total_amount) as income')
            );
        return $list->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();
    }

    public function getIncomes($shop_id = null)
    {
        $order = Order::query();
        if ($shop_id) {
            $order->where('shop_id', $shop_id);
        }
        //$order = Order::query()->where('payment_at', 'like', '%');
        $order = $order->whereNotNull('payment_at');
        $data = [];
        // 今天数据
        $data['today'] = $order->whereDate('created_at', Carbon::today())->get()->pluck('total_amount')->sum();
        // 昨天数据
        $data['yesterday'] = $order->whereDate('created_at', Carbon::yesterday())->get()->pluck('total_amount')->sum();
        // 本周数据
        $this_week = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $data['this_week'] = $order->whereBetween('created_at', $this_week)->get()->pluck('total_amount')->sum();
        // 上周数据
        $last_week = [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()];
        $data['last_week'] = $order->whereBetween('created_at', $last_week)->get()->pluck('total_amount')->sum();
        // 本月数据
        $data['this_month'] = $order->whereMonth('created_at', Carbon::now()->month)->get()->pluck('total_amount')->sum();
        // 上月数据
        $data['last_month'] = $order->whereMonth('created_at', Carbon::now()->subMonth()->month)->get()->pluck('total_amount')->sum();
        // 本年数据
        $data['this_year'] = $order->whereYear('created_at', Carbon::now()->year)->get()->pluck('total_amount')->sum();
        // 上一年
        $data['last_year'] = $order->whereYear('created_at', Carbon::now()->subYear()->year)->get()->pluck('total_amount')->sum();
        return $data;
    }
}
