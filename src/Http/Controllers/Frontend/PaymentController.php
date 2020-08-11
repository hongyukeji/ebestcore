<?php

namespace System\Http\Controllers\Frontend;

use System\Models\PaymentLog;
use Hongyukeji\LaravelPayment\Facades\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use System\Librarys\Payment\Exceptions\PaymentException;
use System\Services\PaymentLogService;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payment_no = $request->input('payment_no');
        $result = (new PaymentLogService())->checkStatus($payment_no);
        if (!$result->verify()) {
            return redirect()->back()->with('error', $result->message);
        }
        $payment_log = $result->data;
        return view('frontend::payments.index', [
            'payment_log' => $payment_log
        ]);
    }

    public function handle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_gateway' => 'required',
            'payment_no' => 'required|exists:payment_logs,payment_no',
        ], [], [
            'payment_gateway' => '支付方式',
            'payment_no' => '支付单号',
        ]);

        // 验证前判断支付方式是否存在
        /*$validator->after(function ($validator) {
            //$validator->errors()->add('payment_type', '支付方式不存在');
        });*/

        // 验证失败跳转
        if ($validator->fails()) {
            return redirect(route('frontend.user.orders.index'))->withErrors($validator)->withInput();
        }
        try {
            $payment_gateway = $request->input('payment_gateway');
            $payment_no = $request->input('payment_no');
            $payment_log = PaymentLog::query()->where('payment_no', $payment_no)->first();
            if (!$payment_log) {
                return redirect()->back()->with('error', '支付记录不存在');
            }
            $order = [
                'order_no' => $payment_log->payment_no,
                'total_amount' => $payment_log->total_amount,
                'subject' => get_site_name() . '-' . $payment_log->payment_no,
            ];
            $payment = new \System\Librarys\Payment\Payment();
            return $payment->gateway($payment_gateway, 'web', $order)->send();
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function result()
    {
        $payment_no = request()->input('payment_no');
        $payment_log = PaymentLog::query()->where('payment_no', $payment_no)->first();

        if (!$payment_log) {
            return redirect()->back()->with('error', '支付记录不存在');
        }

        return view('frontend::payments.result', [
            'payment_log' => $payment_log
        ]);
    }
}
