<?php

namespace System\Librarys\Payment\Interfaces;

interface GatewayInterface
{
    /**
     * 处理支付服务商参数
     *
     * @return mixed
     */
    public function boot();

    /**
     * 电脑端支付
     *
     * @return mixed
     */
    public function web();

    /**
     * 移动端支付
     *
     * @return mixed
     */
    public function wap();

    /**
     * App端支付
     *
     * @return mixed
     */
    public function app();

    /**
     * 验证签名
     *
     * @return mixed
     */
    public function verify();

}
