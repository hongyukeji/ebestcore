<?php

namespace System\Librarys\Payment\Abstracts;

use Illuminate\Support\Arr;
use System\Librarys\Payment\Interfaces\GatewayInterface;

abstract class GatewayAbstract implements GatewayInterface
{
    protected $config;

    protected $params;

    protected $order;

    /**
     * GatewayAbstract constructor.
     *
     * @param array $config
     * @param array $params
     */
    public function __construct(array $config = [], array $params = [])
    {
        $this->config = $config;

        $this->params = $params;

        $this->boot();
    }

    /**
     * 加载类之前执行函数
     */
    public function boot()
    {
        //
    }

    /**
     * 获取当前支付服务商配置项参数
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
     * 设置Params参数
     *
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }
}
