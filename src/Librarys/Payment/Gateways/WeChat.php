<?php

namespace System\Librarys\Payment\Gateways;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use System\Librarys\Payment\Abstracts\GatewayAbstract;
use System\Librarys\Payment\Gateways\WeChat\WeJsApiPay;
use Yansongda\Pay\Pay;

class WeChat extends GatewayAbstract
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
                'total_fee' => intval($this->params['total_amount'] * 100),
                'body' => $this->params['subject'],
            ];
        }
        if (!empty($this->config)) {
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
        $wechat = Pay::wechat($this->config)->scan($this->order);

        /*$params = [
            strtolower(class_basename(__CLASS__)),
            'payment_no' => $this->order['out_trade_no'],
            'code_url' => $wechat->code_url
        ];
        $this->config['return_url'] = route('frontend.payment.return.index', $params);
        return redirect($this->config['return_url']);*/

        $payment_no = $this->order['out_trade_no'];
        $code_url = $wechat->code_url ?? '';
        $img_code_url = route('helper.qr-code.index', ['text' => $code_url, 'size' => 300]);
        return view('frontend::payments.wechat', compact('img_code_url', 'payment_no'));
    }

    /**
     * 移动端支付
     *
     * @return mixed
     */
    public function wap()
    {
        $isWeixinClient = preg_match('/MicroMessenger/i', request()->header('User-Agent'));

        if ($isWeixinClient) {
            $wxPay = new WeJsApiPay($this->config);
            $openId = $wxPay->GetOpenid();   //获取openid
            if (isset($openId)) {
                session(['openid' => $openId]);
            };
            $this->order['openid'] = $openId;
            $this->order['spbill_create_ip'] = get_client_ip();
            $jsApiParameters = Pay::wechat($this->config)->mp($this->order)->send();
            return view('mobile::payments.wechat', compact('jsApiParameters'));
            // 用户在微信中, 并且没有关联 openid
            /*if ($code) {
                // 通过 code 获取 open_id
                $client = new Client();
                $url = sprintf("https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code",
                    $appid, $secret, $code);
                try {
                    $res = $client->request('GET', $url, ['timeout' => 1.5]);
                    $res = $res->getBody();
                    $res = json_decode($res);
                } catch (\Exception $e) {
                    Log::info('Fail to call api');
                }
            } else {
                $callback_url = env('APP_URL') . '/cart';
                // 静默授权，跳转获取 code
                $url = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect",
                    $appid, urlencode($callback_url));
                return redirect($url);
            }*/
        } else {
            return Pay::wechat($this->config)->wap($this->order)->send();
        }
    }

    /**
     * App端支付
     *
     * @return mixed
     */
    public function app()
    {
        return Pay::wechat($this->config)->app($this->order)->send();
    }

    /**
     * 小程序支付
     *
     * @return mixed
     */
    public function miniapp()
    {
        return Pay::wechat($this->config)->miniapp($this->order)->send();
    }

    /**
     * 验证签名
     *
     * @return mixed|\Yansongda\Supports\Collection
     * @throws \Yansongda\Pay\Exceptions\InvalidArgumentException
     * @throws \Yansongda\Pay\Exceptions\InvalidSignException
     */
    public function verify()
    {
        $result = Pay::wechat($this->config)->verify();
        Log::info(self::class, $result->toArray());
        return [
            'order_no' => $result->out_trade_no,
            'trade_no' => $result->transaction_id,
            'total_amount' => $result->total_fee / 100,
            'receipt_amount' => null,
        ];
    }

}
