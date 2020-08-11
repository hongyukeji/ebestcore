<?php

namespace System\Http\Controllers\Api\V1\Payment;

use System\Http\Controllers\Api\Controller;
use System\Models\PaymentLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use System\Librarys\Payment\Payment;
use System\Services\OrderService;

class NotifyController extends Controller
{
    public function index($gateway)
    {
        try {
            $payment = (new Payment())->gateway($gateway, 'verify');
            $result = $payment->verify();
            $order_no = $result['order_no'];
            $total_amount = $result['total_amount'];
            $trade_no = isset($result['trade_no']) ? $result['trade_no'] : '';
            $payment_log = PaymentLog::query()->where([
                'payment_no' => $order_no,
                'total_amount' => $total_amount,
            ])->first();

            if ($payment_log && !$payment_log->status) {
                $payment_log->update([
                    'payment_method' => config("payments.gateways.{$gateway}.name"),
                    'payment_code' => $gateway,
                    'payment_trade_no' => $trade_no,
                    'payment_at' => now(),
                    'status' => PaymentLog::STATUS_PAID,
                ]);
                return $payment->success();
            } else {
                return $payment->fail();
            }
        } catch (\Exception $e) {
            Log::warning("[" . get_class() . "] " . $e->getMessage());
            return abort(503, $e->getMessage());
        }
    }

}
