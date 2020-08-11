<?php

namespace System\Librarys\Payment\Gateways;

use Illuminate\Support\Facades\Log;
use System\Librarys\Payment\Abstracts\GatewayAbstract;
use System\Librarys\Payment\Exceptions\PaymentException;

class PayJs extends GatewayAbstract
{
    private $mchid;
    private $key;
    private $url;
    private $api_url_native;
    private $api_url_cashier;
    private $api_url_refund;
    private $api_url_close;
    private $api_url_check;
    private $api_url_user;
    private $api_url_info;
    private $api_url_bank;
    private $api_url_jsapi;
    private $api_url_facepay;
    private $api_url_openid;
    private $data;

    public function boot()
    {
        $this->mchid = $this->config['mchid'];
        $this->key = $this->config['key'];
        $api_url = 'https://payjs.cn/api/';

        $this->api_url_native = $api_url . 'native';
        $this->api_url_cashier = $api_url . 'cashier';
        $this->api_url_refund = $api_url . 'refund';
        $this->api_url_close = $api_url . 'close';
        $this->api_url_check = $api_url . 'check';
        $this->api_url_user = $api_url . 'user';
        $this->api_url_info = $api_url . 'info';
        $this->api_url_bank = $api_url . 'bank';
        $this->api_url_jsapi = $api_url . 'jsapi';
        $this->api_url_facepay = $api_url . 'facepay';
        $this->api_url_openid = $api_url . 'openid';

        if (!empty($this->params)) {
            $order_no = $this->params['order_no'] ?? time();
            $subject = strlen($this->params['subject']) <= 32 ? $this->params['subject'] : $order_no;
            $total_amount = intval($this->params['total_amount'] * 100);

            // 构造订单基础信息
            $this->order = [
                'body' => $subject,// 订单标题
                'total_fee' => $total_amount,   // 订单金额
                'out_trade_no' => $order_no,    // 订单号
                'attach' => '', // 订单附加信息(可选参数)
                'notify_url' => route('api.v1.payments.notify.index', strtolower(class_basename(__CLASS__))),   // 异步通知地址(可选参数)
            ];
        }
    }

    /**
     * 电脑端支付
     *
     * @return mixed
     * @throws PaymentException
     */
    public function web()
    {
        $data = $this->order;
        $this->url = $this->api_url_native;
        $pay = $this->post($data);

        if (!$pay['return_code']) {
            throw new PaymentException($pay['return_msg']);
        }

        /*$params = [
            strtolower(class_basename(__CLASS__)),
            'payment_no' => $this->order['out_trade_no'],
            'code_url' => $pay['code_url'] ?? ''
        ];
        $this->config['return_url'] = route('frontend.payment.return.index', $params);
        return redirect($this->config['return_url']);*/

        $payment_no = $this->order['out_trade_no'];
        $code_url = $pay['code_url'] ?? '';
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
        $data = $this->order;
        $data['auto'] = 1;
        $data['callback_url'] = route_url('mobile.payments.handle', [
            'payment_gateway' => strtolower(class_basename(__CLASS__)),
            'payment_no' => $this->order['out_trade_no']
        ]);
        if ($this->isWechatBrowser()) {
            // 收银台模式
            $this->url = $this->api_url_cashier;
            $data = $this->sign($data);
            $url = $this->url . '?' . http_build_query($data);
            return redirect($url);

            /*
            // 正常模式
            if (request()->filled('openid')) {
                $data['openid'] = request()->input('openid');
                $this->url = $this->api_url_jsapi;
                $jsApiParameters = $this->post($data);
                return view('mobile::payments.wechat', compact('jsApiParameters'));
            } else {
                // 获取用户 OPENID
                // https://help.payjs.cn/api-lie-biao/huo-qu-openid.html
                return $this->openid($this->config['mchid'], request()->getUri());
            }
            */
        } else {
            return redirect()->back()->with('error', '请在微信客户端中进行支付');
        }
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
        $result = request()->all();
        if ($this->checkSign($result) !== true) {
            return null;
        }
        Log::info(self::class, $result);
        return [
            'order_no' => $result['out_trade_no'] ?? '',
            'trade_no' => $result['payjs_order_id'] ?? '',
            'total_amount' => $result['total_fee'] / 100 ?? '',
        ];
    }

    /**
     * 获取openid
     *
     * @param string $mchid 商户号
     * @param string $callback_url 接收 openid 的 url。必须为可直接访问的url，不能带session验证、csrf验证
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function openid($mchid = '', $callback_url = '')
    {
        $data = [
            'mchid' => $mchid,
            'callback_url' => $callback_url,
        ];
        $url = $this->api_url_openid . '?' . http_build_query($data);
        return redirect($url);
    }

    // 扫码支付
    public function native(array $data)
    {
        /*
        $data = [
            'body' => '订单测试',                                // 订单标题
            'total_fee' => 2,                                   // 订单标题
            'out_trade_no' => time(),                           // 订单号
            'attach' => 'test_order_attach',                    // 订单附加信息(可选参数)
            'notify_url' => 'https://www.baidu.com/notify',     // 异步通知地址(可选参数)
        ];
        */
        $this->url = $this->api_url_native;
        return $this->post($data);
    }

    // 收银台模式
    public function cashier(array $data)
    {
        /*
        $data = [
            'body' => '订单测试',                                    // 订单标题
            'total_fee' => 2,                                       // 订单金额
            'out_trade_no' => time(),                               // 订单号
            'attach' => 'test_order_attach',                        // 订单附加信息(可选参数)
            'notify_url' => 'https://www.baidu.com/notify',         // 异步通知地址(可选参数)
            'callback_url' => 'https://www.baidu.com/callback',     // 支付后前端跳转地址(可选参数)
        ];
        */
        $this->url = $this->api_url_cashier;
        $data = $this->sign($data);
        $url = $this->url . '?' . http_build_query($data);
        return $url;
    }

    // JASAPI
    public function jsapi(array $data)
    {
        /*
        $data = [
            'body' => '订单测试',                                    // 订单标题
            'total_fee' => 2,                                       // 订单金额
            'out_trade_no' => time(),                               // 订单号
            'attach' => 'test_order_attach',                        // 订单附加信息(可选参数)
            'openid' => 'xxxxxxxxxxxxxxxxx',                        // 订单附加信息(可选参数)
            'notify_url' => 'https://www.baidu.com/notify',         // 异步通知地址(可选参数)
        ];
        */
        $this->url = $this->api_url_jsapi;
        return $this->post($data);
    }

    // 退款
    public function refund($payjs_order_id)
    {
        $this->url = $this->api_url_refund;
        $data = ['payjs_order_id' => $payjs_order_id];
        return $this->post($data);
    }

    // 人脸支付
    public function facepay(array $data)
    {
        $this->url = $this->api_url_facepay;
        return $this->post($data);
    }

    // 关闭订单
    public function close($payjs_order_id)
    {
        $this->url = $this->api_url_close;
        $data = ['payjs_order_id' => $payjs_order_id];
        return $this->post($data);
    }

    // 检查订单
    public function check($payjs_order_id)
    {
        $this->url = $this->api_url_check;
        $data = ['payjs_order_id' => $payjs_order_id];
        return $this->post($data);
    }

    // 用户资料
    public function user($openid)
    {
        $this->url = $this->api_url_user;
        $data = ['openid' => $openid];
        return $this->post($data);
    }

    // 商户资料
    public function info()
    {
        $this->url = $this->api_url_info;
        $data = [];
        return $this->post($data);
    }

    // 银行资料
    public function bank($name)
    {
        $this->url = $this->api_url_bank;
        $data = ['bank' => $name];
        return $this->post($data);
    }

    // 异步通知接收
    public function notify()
    {
        $data = request()->all();
        if ($this->checkSign($data) === true) {
            return $data;
        } else {
            return '验签失败';
        }
    }

    // 数据签名
    public function sign(array $data)
    {
        $data['mchid'] = $this->mchid;
        $data = array_filter($data);
        ksort($data);
        $data['sign'] = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->key)));
        return $data;
    }

    // 校验数据签名
    public function checkSign($data)
    {
        $in_sign = data_get($data, 'sign');
        unset($data['sign']);
        $data = array_filter($data);
        ksort($data);
        $sign = strtoupper(md5(urldecode(http_build_query($data) . '&key=' . $this->key)));
        return $in_sign == $sign ? true : false;
    }

    // 数据发送
    public function post($data)
    {
        $data = $this->sign($data);
        $client = new \GuzzleHttp\Client([
            'header' => ['User-Agent' => 'PAYJS Larevel Http Client'],
            'timeout' => 10,
            'http_errors' => false,
            'defaults' => ['verify' => false],
        ]);
        $rst = $client->request('POST', $this->url, ['form_params' => $data]);
        return json_decode($rst->getBody()->getContents(), true);
    }

    public function isWechatBrowser()
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return true;
        }
        return false;
    }
}
