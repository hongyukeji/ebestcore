<?php

namespace System\Librarys\Payment;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use System\Librarys\Payment\Exceptions\PaymentException;

class Payment
{
    /*
     * 所有支付配置
     */
    protected $configs;

    /*
     * 支付配置
     */
    protected $config;

    /*
     * 支付服务商
     */
    protected $gateway;

    /*
     * 支付方式
     */
    protected $method;

    /*
     * 支付参数
     */
    protected $params;

    /**
     * Payment constructor.
     *
     * @param array $configs
     */
    public function __construct(array $configs = [])
    {
        if (!empty($configs)) {
            $this->configs = $configs;
        } else {
            $this->configs = config('payments');
        }
    }

    /**
     * 支付服务商
     *
     * @param $gateway
     * @param $method [web, wap, app]
     * @param array $params [ 'order_no' => '201900000001', 'total_amount' => '0.01', 'subject' => '测试订单'];
     * @return $this
     * @throws PaymentException
     */
    public function gateway($gateway, $method = null, $params = [])
    {
        $gateway_name = $gateway;

        /*$dir = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'Gateways';
        if (!is_dir($dir)) {
            throw new PaymentException('支付服务商目录不存在');
        }
        $gateways = array_diff(scandir($dir), ['.', '..']);
        $class_name = null;
        foreach ($gateways as $gateway) {
            $original_name = Str::before($gateway, '.php');
            if (strcasecmp($original_name, $gateway_name) == 0) {
                $class_name = $original_name;
                break;
            }
        }*/

        $gateway_class_name = $this->getConfigs("gateways.{$gateway_name}.driver");
        if (!class_exists($gateway_class_name)) {
            $message = '[' . $gateway_name . ']支付服务商不存在';
            Log::warning($message);
            throw new PaymentException($message);
        }

        $this->config = $this->getConfigs("gateways.{$gateway_name}.options");
        if (!is_array($this->config)) {
            $message = '[' . $gateway_name . ']支付服务商配置参数不存在';
            Log::warning($message);
            throw new PaymentException($message);
        }

        $this->params = $params;
        $this->gateway = new $gateway_class_name($this->config, $params);

        if (!method_exists($this->gateway, $method)) {
            $message = '[' . $method . ']支付方法不存在';
            Log::warning($message);
            throw new PaymentException($message);
        }
        $this->method = $method;

        return $this;
    }

    /**
     * 发起支付
     *
     * Payment::gateway('alipay')->method('web')->pay($order);
     *
     * @return mixed
     */
    public function send()
    {
        return $this->gateway->setParams($this->params)->{$this->method}();
    }

    /**
     * 验证签名
     *
     * @return mixed
     */
    public function verify()
    {
        return $this->gateway->verify();
    }

    /**
     * 获取配置参数
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getConfigs($key, $default = null)
    {
        return Arr::get($this->configs, $key, $default);
    }

    /**
     * 设置配置参数
     *
     * @param $configs
     * @return $this
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
        return $this;
    }

    /**
     * 获取配置参数
     *
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function getConfig($key, $default = null)
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * 设置配置参数
     *
     * @param $config
     * @return $this
     */
    public function setConfig($config)
    {
        $this->config = $config;
        return $this;
    }

    /**
     * 支付成功
     *
     * @return mixed
     */
    public function success()
    {
        return Response::create('success');
    }

    /**
     * 支付失败
     *
     * @return mixed
     */
    public function fail()
    {
        return Response::create('fail');
    }
}
