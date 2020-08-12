<?php

namespace System\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use System\Librarys\Supports\Collection;
use System\Models\Cart;
use System\Models\OrderDetail;
use System\Models\PaymentLog;
use System\Models\Order;
use System\Models\Product;
use System\Models\ProductSku;
use System\Models\Shop;
use System\Models\UserAddress;
use System\Models\UserInvoice;

class OrderService extends Service
{
    /*
     * 检查购物车商品
     */
    public function checkCartData($cart_ids, $user_id = null)
    {
        // 如果用户id为空, 且用户已登录, 则自动获取当前用户id
        if (is_null($user_id) && Auth::check()) {
            $user_id = Auth::id();
        }

        // 如果传入的购物车id不是数组格式, 则转换为数组格式
        if (!is_array($cart_ids)) {
            $cart_ids = explode(',', $cart_ids);
        }

        // 查找当前用户购物车id
        $carts = Cart::query()->where('user_id', $user_id)->whereIn('id', $cart_ids)->get();

        // 检查购物车数组是否为空
        if (!$carts->count()) {
            return return_result()->failed('未选择有效的购物车商品');
        }

        // 检查商品状态\库存是否正常
        foreach ($carts as $cart) {
            // 检查商品状态
            if (is_null($cart->product->status)) {
                return return_result()->failed('购物车中商品ID为[' . $cart->product_id . ']的商品不存在');
            }
            if ($cart->product->status != Product::STATUS_ON_SALE) {
                return return_result()->failed('[' . $cart->product->name . ']商品未上架');
            }

            // 检查库存
            if ($cart->product->stock < $cart->number) {
                return return_result()->failed($cart->product->name . '库存不足');
            }
            if ($cart->product_sku_id > 0 && $cart->productSku->stock < $cart->number) {
                return return_result()->failed($cart->product->name . 'Sku属性' . $cart->productSku->name . '库存不足');
            }

            // 检查商家状态
            if ($cart->product->shop->id > 0 && $cart->product->shop->status != Shop::STATUS_ACTIVE) {
                return return_result()->failed('购物车中[' . $cart->product->name . ']商品的店铺状态不正常');
            }
        }

        return return_result()->success($carts);
    }

    /*
     * 获取购物车订单信息
     */
    public function getCartOrderInfo($cart_ids, $user_id = null)
    {
        $result = $this->checkCartData($cart_ids, $user_id);
        if (!$result->verify()) {
            return return_result()->failed($result->message);
        }
        $carts = $result->get('data');
        $total_price = 0;
        $support_invoice = false;

        foreach ($carts as $cart) {
            // 总金额计算
            $total_price += $cart->product_subtotal;

            // 是否开发票
            if ($cart->shop->support_invoice) {
                $support_invoice = true;
            }
            if ($cart->shop->id <= 0 && config('shop.support_invoice')) {
                $support_invoice = true;
            }
        }

        $order_info = collects([
            'carts' => $carts,
            'total_number' => $carts->sum('number'),
            'total_amount' => number_format($total_price, 2, '.', ''),
            'total_freight' => 0,
            'support_invoice' => $support_invoice,
        ]);
        return return_result()->success($order_info);
    }

    /*
     * 生成购物车订单
     */
    public function generateCartOrder($cart_ids = null, $user_id = null)
    {
        if (!isset($request)) {
            $request = request();
        }
        // 如果用户id为空, 且用户已登录, 则自动获取当前用户id
        if (is_null($user_id) && Auth::check()) {
            $user_id = Auth::id();
        }
        if (is_null($cart_ids)) {
            $cart_ids = $request->input('carts');
        }

        // 开启一个数据库事务
        $data = DB::transaction(function () use ($request, $cart_ids, $user_id) {
            $cart_order_info = $this->getCartOrderInfo($cart_ids, $user_id);
            if (!$cart_order_info->verify()) {
                return return_result()->failed($cart_order_info->message);
            }

            // 购物车订单信息
            $order_info = $cart_order_info->get('data');

            // 购物车商品集合
            $carts = $order_info->carts;

            // 获取当前用户信息
            $user = auth()->user();

            // 判断用户收货地址是否存在
            $address_id = $request->input('address_id');
            if (is_null($address_id)) {
                return return_result()->failed('请选择收货地址，收货地址不能为空');
            }

            $address = UserAddress::query()->where('user_id', $user_id)->find($address_id);
            if (!$address) {
                return return_result()->failed('请选择有效的收货地址');
            }

            //判断用户发票是否存在
            if ($request->filled('invoice_id')) {
                $invoice = UserInvoice::query()->where('user_id', $user_id)->find($request->input('invoice_id'));
                if (!$invoice) {
                    return return_result()->failed('用户发票不存在');
                }
            }

            // 订单备注 $remarks[店铺编号] = '备注内容';
            $remarks = $request->input('remarks');

            // 生成支付日志
            $payment_log = PaymentLog::create([
                'payment_no' => create_order_no(),
                'payment_type' => PaymentLog::PAYMENT_TYPE_ORDER,
                'total_amount' => 0,
                'status' => PaymentLog::STATUS_UNPAID,
            ]);

            $shops = $carts->groupBy('shop_id');

            foreach ($shops as $shop_id => $carts) {
                // 根据店铺id创建店铺订单
                $order_no = build_number_no();
                $device = get_client_os();
                $first_shop = $carts->first()->shop;

                $order = Order::create([
                    'order_no' => $order_no,
                    'order_source' => $device,
                    'payment_log_id' => $payment_log->id,
                    'shop_id' => $first_shop->id,
                    'shop_name' => $first_shop->name,
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'consignee_name' => $address->consignee,
                    'consignee_phone' => $address->phone,
                    'consignee_address' => $address->province . $address->city . $address->district . $address->address,
                    'user_address_id' => $address->id,
                    'address' => json_encode($address->toArray()),
                    'user_invoice_id' => !empty($invoice) && $first_shop->support_invoice ? $invoice->id : null,
                    'invoice' => !empty($invoice) && $first_shop->support_invoice ? json_encode($invoice->toArray()) : null,
                    'total_number' => 0,    // 根据订单详情自增数量
                    'total_amount' => 0,    // 根据订单详情自增金额
                    'remark' => !empty($remarks[$first_shop->id]) ? $remarks[$first_shop->id] : '',
                    'status' => Order::STATUS_UNPAID,
                ]);
                foreach ($carts as $cart) {
                    $order_detail = OrderDetail::create([
                        'order_id' => $order->id,
                        'product' => json_encode($cart->product->toArray()),
                        'product_sku' => json_encode($cart->productSku->toArray()),
                        'product_id' => $cart->product->id,
                        'product_spu_code' => $cart->product->spu_code,
                        'product_sku_id' => $cart->product_sku_id,
                        'product_sku_code' => $cart->productSku->sku_code,
                        'product_sku_name' => $cart->productSku->name,
                        'product_name' => $cart->product->name,
                        'product_image' => $cart->product->image,
                        'product_price' => $cart->product_price,
                        'number' => $cart->number,
                        'subtotal' => number_format($cart->product_subtotal, 2, '.', ''),
                    ]);

                    // 自增订单总数量和总金额
                    $order->increment('total_number', $order_detail->number);
                    $order->increment('total_amount', $order_detail->subtotal);
                }

                // 自增支付日志总金额
                $payment_log->increment('total_amount', $order->total_amount);

                // 用户下单事件
                event(new \System\Events\Orders\OrderCreateEvent($order));
            }

            // 删除购物车中已结算商品
            Cart::destroy($carts->pluck('id'));

            // 返回支付日志模型
            return return_result()->success($payment_log);
        });

        return $data;
    }

    /**
     * 更新订单支付状态
     *
     * @param PaymentLog $payment_log
     * @return bool
     */
    public function updatePaymentStatus(PaymentLog $payment_log)
    {
        if (isset($payment_log->orders) && count($payment_log->orders)) {
            // 判断订单日志总金额和关联订单总金额是否相等
            if ($payment_log->total_amount == $payment_log->orders->sum('total_amount')) {
                $orders = Order::query()->where('payment_log_id', $payment_log->id)->get();
                // 相等 - 循环更改订单状态为已支付
                foreach ($orders as $order) {
                    $order->update([
                        'payment_trade_no' => $payment_log->payment_trade_no,
                        'payment_code' => $payment_log->payment_code,
                        'payment_method' => $payment_log->payment_method,
                        'payment_at' => $payment_log->payment_at ?? now(),
                        'status' => Order::STATUS_WAIT_DELIVERY,
                    ]);
                    /*$order->payment_at = $payment_log->payment_at;
                    $order->status = Order::STATUS_PAID;
                    $order->save();*/
                }
                return true;
            }
        }

        // 不相等 - 查找符合 订单日志总金额 的订单
        return false;
    }

    /**
     * 关闭未付款订单
     *
     * @param Order $order
     */
    public function closeUnpaidOrder(Order $order)
    {
        if ($order->status_paid) {
            return;
        }
        // 通过事务执行 sql
        DB::transaction(function () use ($order) {
            // 更新订单状态为已取消
            $order->update([
                'closed_at' => now(),
                'status' => Order::STATUS_CANCELLED,
            ]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU 的库存中去
            foreach ($order->details as $item) {
                $product_sku_id = $item->product_sku_id;
                $product = Product::query()->findOrFail($item->product_id);
                // 根据商品库存计数方式操作,判断是否增加商品库存
                if ($product->stock_count_mode == Product::STOCK_COUNT_MODE_PLACE_ORDER && empty($item->stock_count_at)) {
                    if ($product_sku = ProductSku::query()->find($product_sku_id)) {
                        $product_sku->increment('stock', $item->number);
                    } else {
                        $product->increment('stock', $item->number);
                    }
                    $item->update([
                        'stock_count_at' => now(),
                    ]);
                }
            }
        });
    }
}
