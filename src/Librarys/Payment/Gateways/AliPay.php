<?php

namespace System\Librarys\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use System\Librarys\Payment\Abstracts\GatewayAbstract;
use Yansongda\Pay\Pay;

class AliPay extends GatewayAbstract
{
    /**
     * 处理支付服务商参数
     *
     * @return mixed
     */
    public function boot()
    {
        if (!empty($this->params)) {
            $this->order = [
                'out_trade_no' => $this->params['order_no'],
                'total_amount' => $this->params['total_amount'],
                'subject' => $this->params['subject'],
            ];
        }
        if (!empty($this->config) && is_array($this->config)) {
            $this->config['notify_url'] = route('api.v1.payments.notify.index', strtolower(class_basename(__CLASS__)));
        }
    }

    /**
     * 电脑端支付
     *
     * @return mixed
     */
    public function web()
    {
        if (request()->filled('out_trade_no')) {
            return redirect(route_url('frontend.payments.result', ['payment_no' => request()->input('out_trade_no')]));
        } else {
            $this->config['return_url'] = route_url('frontend.payments.handle', [
                'payment_gateway' => strtolower(class_basename(__CLASS__)),
                'payment_no' => $this->order['out_trade_no']
            ]);
            return Pay::alipay($this->config)->web($this->order)->send();
        }
    }

    /**
     * 移动端支付
     *
     * @return mixed
     */
    public function wap()
    {
        // TODO: 微信浏览器中提示在浏览器中打开
        if ($this->isBrowserDetectIsWeChat()) {
            return redirect(route_url('mobile.payment.alipay-tips'))->with('message', '对不起，微信浏览器中不支持支付宝！');
        }
        $this->config['return_url'] = route_url('mobile.payments.handle', [
            'payment_gateway' => strtolower(class_basename(__CLASS__)),
            'payment_no' => $this->order['out_trade_no']
        ]);
        return Pay::alipay($this->config)->wap($this->order)->send();
    }

    /**
     * App端支付
     *
     * @return mixed
     */
    public function app()
    {
        return Pay::alipay($this->config)->app($this->order)->send();
    }

    /**
     * 小程序支付
     *
     * @return mixed
     */
    public function miniapp()
    {
        return Pay::alipay($this->config)->mini($this->order)->send();
    }

    /**
     * 验证签名
     *
     * @return mixed|\Yansongda\Supports\Collection
     * @throws \Yansongda\Pay\Exceptions\InvalidConfigException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function verify()
    {
        $result = Pay::alipay($this->config)->verify();
        Log::info(self::class, $result->toArray());
        return [
            'order_no' => $result->out_trade_no,
            'trade_no' => $result->trade_no,
            'total_amount' => $result->total_amount,
            'receipt_amount' => $result->receipt_amount,
        ];
    }

    /*
     * 判断是否是微信浏览器
     */
    private function isBrowserDetectIsWeChat()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}
