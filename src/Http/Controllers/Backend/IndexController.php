<?php

namespace System\Http\Controllers\Backend;

use System\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use System\Repository\Interfaces\OrderInterface;
use System\Repository\Interfaces\UserInterface;
use System\Repository\Interfaces\ProductInterface;

class IndexController extends Controller
{
    protected $product;
    protected $user;
    protected $order;

    public function __construct(ProductInterface $product, UserInterface $user, OrderInterface $order)
    {
        $this->product = $product;
        $this->user = $user;
        $this->order = $order;
    }

    public function index()
    {
        $pages = [
            'title' => '仪表盘',
            'description' => '概览',
            'breadcrumbs' => [
                [
                    'name' => '首页',
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ],
                [
                    'name' => '仪表盘',
                    'icon' => 'fa fa-dashboard',
                    'link' => '',
                    'active' => true,
                ],
            ],
        ];

        $stats = [
            'user_count' => $this->user->createCount(),
            'order_count' => $this->order->createCount(),
            //'product_count' => $this->product->createCount(),
        ];
        $stats['user_count']['percentage'] = number_calc_percentage($stats['user_count']['yesterday_count'], $stats['user_count']['today_count']);
        $stats['order_count']['percentage'] = number_calc_percentage($stats['order_count']['yesterday_count'], $stats['order_count']['today_count']);
        //$stats['product_count']['percentage'] = number_calc_percentage($stats['product_count']['yesterday_count'], $stats['product_count']['today_count']);

        if ($stats['user_count']['percentage'] <= 0) {
            $stats['user_count']['percentage'] = 0;
        }

        $orders = [
            'incomes' => $this->order->getIncomes(),
            'daily_income' => $this->order->getDailyIncome(),
        ];
        $orders['percentage'] = number_calc_percentage($orders['incomes']['yesterday'], $orders['incomes']['today']);

        // 订单图表
        $orders_chart = $this->order->queryTotalAmount(request('order_days', 7));

        return view('backend::index.index', compact('stats', 'orders', 'orders_chart', 'pages'));
    }
}
