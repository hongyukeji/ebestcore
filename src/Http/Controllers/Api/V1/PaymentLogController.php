<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Controllers\Api\Controller;
use System\Models\PaymentLog;
use System\Http\Resources\PaymentLogResource;

class PaymentLogController extends Controller
{
    public function show($id)
    {
        $payment_log = PaymentLog::query()->where('id', $id)->first() or abort(404);
        return api_result(0, null, new PaymentLogResource($payment_log));
    }
}
