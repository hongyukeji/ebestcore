<?php

namespace System\Notifications;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Config;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use System\Channels\SmsChannel;
use System\Services\VerifyCodeService;

class VerifyMobileNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [SmsChannel::class];
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        $mobile = $notifiable->getMobileForVerification();
        $id = $notifiable->getKey();

        $verifyCodeService = new VerifyCodeService();
        $verify_code_key_prefix = config('params.cache.backend.verify_code.prefix', 'backend_verify_code_');
        $verify_code = config('sms.config.debug', false) ? config('sms.config.default_verify_code', '1234') : mt_rand(1000, 9999);    // 验证码
        $expires_in = config('params.cache.backend.verify_code.expires_at', 60 * 15);  // 过期时间, 单位秒

        // 发送短信验证码
        $result = $verifyCodeService->sendSmsVerifyCode($mobile, ['verify_code' => $verify_code,]);
        if (isset($result['status_code']) && $result['status_code'] == 0) {
            // 将验证码保存至缓存中
            $verifyCodeService->saveVerifyCode($verify_code_key_prefix, $mobile, $verify_code, $expires_in);
            return true;
        } else {
            if (app()->isLocal()) {
                loggers('会员手机号验证发送短信验证码失败', $result);
            }
            $error = isset($result['message']) ? $result['message'] : null;
            throw new \Exception($error);
        }
    }

}
