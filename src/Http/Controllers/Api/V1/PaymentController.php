<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Controllers\Api\Controller;
use System\Models\PaymentLog;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function order(Request $request)
    {
        $payment_log_id = $request->input('payment_log_id');
        $payment_gateway = $request->input('payment_gateway');
        $payment_method = $request->input('payment_method');
        $payment_params = $request->input('payment_params');

        // 获取支付日志
        $payment_log = PaymentLog::query()->find($payment_log_id);
        if (!$payment_log) {
            return api_result(1, '支付日志不存在');
        }
        // 判断是否已支付
        if ($payment_log->status) {
            return api_result(1, '订单已支付');
        }
        $order = [
            'order_no' => $payment_log->payment_no,
            'total_amount' => $payment_log->total_amount,
            'subject' => config('websites.basic.site_name', config('app.name')) . '-' . $payment_log->payment_no,
        ];
        if (isset($payment_params['return_url'])) {
            $order['return_url'] = $payment_params['return_url'];
        }
        $payment = new \System\Librarys\Payment\Payment();
        return $payment->gateway($payment_gateway, $payment_method, $order)->send();
    }
}
