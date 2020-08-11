<?php

namespace System\Http\Controllers\Api\V1\Payment;

use System\Http\Controllers\Api\Controller;
use System\Models\PaymentLog;
use Illuminate\Http\Request;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $payment_no = $request->input('payment_no');
        $payment_log = PaymentLog::query()->where(['payment_no' => $payment_no,])->first();
        if (!$payment_log) {
            return api_result(1, '支付日志不存在');
        }
        return api_result(0, null, $payment_log->toArray());
    }
}
