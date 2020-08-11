<?php

namespace System\Traits\Services;

use Illuminate\Support\Facades\Mail;

trait EmailVerifyCodeTrait
{
    public function sendEmailVerifyCode($addressee, $param = [])
    {
        $verify_code = isset($param['verify_code']) ? $param['verify_code'] : mt_rand(1000, 9999);    // 验证码
        try {
            Mail::raw('您的验证码是: ' . $verify_code, function ($message) use ($addressee) {
                $to = $addressee;
                $message->to($to)->subject(config('websites.basic.site_name', config('app.name')) . ' - 验证码');
            });
            return api_result(0);
        } catch (\Exception $e) {
            return api_result(1, $e->getMessage());
        }
    }
}