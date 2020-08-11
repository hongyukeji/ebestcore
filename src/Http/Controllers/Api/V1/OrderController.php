<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Controllers\Api\Controller;
use System\Models\Cart;
use System\Models\Order;
use System\Models\OrderDetail;
use System\Models\PaymentLog;
use System\Models\UserAddress;
use Illuminate\Http\Request;
use System\Http\Resources\CartResource;
use System\Services\OrderService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $cart_ids = explode(',', $request->input('cart_ids'));
        $carts = Cart::query()->find($cart_ids);

        if (!isset($carts) || count($carts) < 1) {
            return api_result(1, '请选择购车中的商品进行结算');
        }

        // 判断购物车结算编号是否包含库存不足商品
        foreach ($carts as $cart) {
            if ($cart->stock < $cart->number) {
                return api_result(1, $cart->product->name . '库存不足');
            }
        }

        // 总金额
        $total_price = 0;
        foreach ($carts as $cart) {
            $price = $cart->productSku->price > 0 ? $cart->productSku->price : $cart->product->price;
            $total_price += number_format($price * $cart->number, 2, '.', '');
        }

        $order = [
            'total_number' => $carts->sum('number'),
            'total_amount' => $total_price,
            'total_freight' => 0,
        ];
        return api_result(0, null, [
            'carts' => CartResource::collection($carts),
            'order' => $order,
        ]);
    }

    public function store(Request $request)
    {
        // 收货地址编号
        $address_id = $request->input('address_id');
        // 判断收货地址编号是否存在
        $address = UserAddress::query()->find($address_id);
        if (!$address) {
            return api_result(1, '收货地址不存在');
        }

        // 购物车ids
        $cart_ids = explode(',', $request->input('cart_ids'));
        // 判断购物车编号是否存在
        $carts = Cart::query()->find($cart_ids);
        if (!isset($carts) || count($carts) < 1) {
            return api_result(1, '请选择购车中的商品进行结算');
        }

        // 订单备注 $remarks[店铺编号] = '备注内容';
        $remarks = $request->input('remarks');

        // 判断购物车结算编号是否包含库存不足商品
        foreach ($carts as $cart) {
            if ($cart->stock < $cart->number) {
                return api_result(1, $cart->product->name . '库存不足');
            }
        }

        $payment_log = PaymentLog::create([
            'payment_no' => create_order_no(),
            'payment_type' => PaymentLog::PAYMENT_TYPE_ORDER,
            'total_amount' => 0,
            'status' => PaymentLog::STATUS_UNPAID,
        ]);

        // 创建订单
        $user = auth()->user();
        $shops = $carts->groupBy('shop_id');
        foreach ($shops as $shop) {
            $order_no = build_number_no();
            $device = get_client_os();
            $order = Order::create([
                'order_no' => $order_no,
                'order_source' => $device,
                'payment_log_id' => $payment_log->id,
                'shop_id' => $carts->first()->shop->id,
                'shop_name' => $carts->first()->shop->name,
                'user_id' => $user->id,
                'user_name' => $user->name,
                'consignee_name' => $address->consignee,
                'consignee_phone' => $address->phone,
                'consignee_address' => $address->province . $address->city . $address->district . $address->address,
                'address' => json_encode($address->toArray()),
                'total_number' => 0,    // 根据订单详情自增数量
                'total_amount' => 0,    // 根据订单详情自增金额
                'remark' => isset($remarks[$carts->first()->shop->id]) ? $remarks[$carts->first()->shop->id] : '',
                'status' => Order::STATUS_UNPAID,
            ]);
            // 创建店铺订单
            foreach ($shop as $cart) {
                $order_detail = OrderDetail::create([
                    'order_id' => $order->id,
                    'product' => json_encode($cart->product->toArray()),
                    'product_sku' => json_encode($cart->productSku->toArray()),
                    'product_id' => $cart->product->id,
                    'product_sku_id' => $cart->product_sku_id,
                    'product_sku_name' => $cart->productSku->name,
                    'product_name' => $cart->product->name,
                    'product_image' => $cart->product->image,
                    'product_price' => $cart->price,
                    'number' => $cart->number,
                    'subtotal' => number_format($cart->price * $cart->number, 2, '.', ''),
                ]);

                // 自增订单总数量和总金额
                $order->increment('total_number', $order_detail->number);
                $order->increment('total_amount', $order_detail->subtotal);
            }

            // 自增支付日志总金额
            $payment_log->increment('total_amount', $order->total_amount);

        }

        // 删除购物车中已结算商品
        Cart::destroy($carts->pluck('id'));

        // 返回支付日志模型
        return api_result(0, null, $payment_log);
    }
}
