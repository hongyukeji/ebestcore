<?php

namespace System\Listeners\Users;

use System\Events\Users\UserRegister;
use System\Models\UserExtend;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jenssegers\Agent\Agent;
use System\Services\VerifyCodeService;

class RecordUserRegisterInfoListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UserRegister $event
     * @return void
     */
    public function handle(UserRegister $event)
    {
        // 判断手机号是否验证
        $request = request();
        if ($request->filled('mobile_verify_code')) {
            $request->user()->update(['mobile_verified_at' => now()]);
        }

        $agent = new Agent();
        $device = $agent->device();  // 设备
        $ip = $request->getClientIp(); // IP地址
        $port = $request->getPort();   // 端口号

        // 将会员注册信息写入数据库
        UserExtend::firstOrCreate([
            'user_id' => $event->user->id
        ], [
            'register_device' => $device,
            'register_ip' => $ip,
            'register_port' => $port,
        ]);

        try {
            $verifyCodeService = new VerifyCodeService();
            $verify_code_key_prefix = config('params.cache.backend.verify_code.prefix', 'backend_verify_code_');
            if (isset($event->user->mobile) && !empty($event->user->mobile)) {
                $verifyCodeService->clearVerifyCode($verify_code_key_prefix, $event->user->mobile);
            }
        } catch (\Exception $e) {
            // 清除短信验证码缓存失败
        }
    }
}
