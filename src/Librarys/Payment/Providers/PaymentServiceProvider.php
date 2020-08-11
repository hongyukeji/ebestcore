<?php

namespace System\Librarys\Payment\Providers;

use Illuminate\Support\ServiceProvider;
use System\Librarys\Payment\Payment;

class PaymentServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('payment', function () {
            return new Payment();
        });
    }
}
