<?php

namespace System\Librarys\Payment\Gateways;

use System\Librarys\Payment\Abstracts\GatewayAbstract;
use System\Models\PaymentLog;

class BankWire extends GatewayAbstract
{
    /**
     * 电脑端支付
     *
     * @return mixed
     */
    public function web()
    {
        $bank_info = $this->config;
        return view('frontend::payments.bankwire', compact('bank_info'));
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
}
