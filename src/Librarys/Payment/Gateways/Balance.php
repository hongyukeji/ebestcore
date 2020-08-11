<?php

namespace System\Librarys\Payment\Gateways;

use System\Models\PaymentLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use System\Librarys\Payment\Abstracts\GatewayAbstract;

class Balance extends GatewayAbstract
{

    /**
     * 电脑端支付
     *
     * @return mixed
     */
    public function web()
    {
        /*return redirect(route('frontend.payment.return.index', [
            strtolower(class_basename(__CLASS__)),
            'payment_no'=> $this->params['order_no'] ?? '',
        ]));*/
        $request = request();
        if (!Auth::check()) {
            return redirect()->back()->with('error', '对不起，请登录您的账号后进行余额支付');
        }

        $user = auth()->user();

        $payment_no = $request->input('payment_no');
        $payment_log = PaymentLog::query()->where('payment_no', $payment_no)->first();
        if (!$payment_log) {
            return redirect()->back()->with('error', '支付记录不存在');
        }
        if ($payment_log->status) {
            return redirect()->back()->with('error', '订单已支付');
        }

        if ($user->account->money < $payment_log->total_amount) {
            return redirect()->back()->with('error', '当前余额不足以支付该订单金额');
        }

        if ($request->isMethod('post')) {
            $payment_password = $request->input('payment_password', '');

            // 判断支付密码是否存在
            $user_payment_password = $user->extend->payment_password;
            if (!empty($user_payment_password)) {
                if (!Hash::check($payment_password, $user_payment_password)) {
                    return redirect()->back()->with('error', '支付密码错误');
                }
            }

            // 更新余额
            $user->account->decrement('money', $payment_log->total_amount);
            // 更新支付日志
            $gateway = 'balance';
            $payment_log->update([
                'payment_method' => config("payments.gateways.{$gateway}.name"),
                'payment_code' => $gateway,
                'payment_trade_no' => uuid(),
                'status' => PaymentLog::STATUS_PAID,
            ]);

            return redirect(route_url('frontend.payments.result', ['payment_no' => $payment_log->payment_no]));
        }

        return view('frontend::payments.balance', compact('payment_log', 'user'));
    }

    /**
     * 移动端支付
     *
     * @return mixed
     */
    public function wap()
    {
        // TODO: Implement wap() method.
    }

    /**
     * App端支付
     *
     * @return mixed
     */
    public function app()
    {
        // TODO: Implement app() method.
    }

    /**
     * 验证签名
     *
     * @return mixed
     */
    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
