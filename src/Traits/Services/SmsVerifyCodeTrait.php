<?php

namespace System\Traits\Services;

trait SmsVerifyCodeTrait
{
    public function sendSmsVerifyCode($mobile, $param = [])
    {
        $verify_code = !empty($param['verify_code']) ? $param['verify_code'] : mt_rand(1000, 9999);    // 验证码

        if (config('sms.config.debug', false)) {
            $verify_code = config('sms.config.default_verify_code', '1234');
        } else {
            $templateCode = 'verify_code';    // 验证码短信模板code
            $templateParam = ['code' => $verify_code];    // 短信参数 ( 鉴于阿里限制短信验证码只能传一个参数, 此处改为传一个code )
            $sms = sms_send($mobile, $templateCode, $templateParam); // 发送短信
            // 发送失败
            if ($sms['status'] !== 'success') {
                return api_result(1, $sms['message'], $sms);
            }
        }

        return api_result(0);
    }
}
