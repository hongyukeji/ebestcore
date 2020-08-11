<?php

namespace System\Listeners\Users;

use System\Events\Users\UserLogin;
use System\Models\UserLogin as UserLoginModel;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jenssegers\Agent\Agent;

class RecordUserLoginInfoListener
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
     * @param UserLogin $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
        $agent = new Agent();
        $device = $agent->device();
        $ip = request()->getClientIp(); // IP地址
        $port = request()->getPort();   // 端口号

        // 将会员登录信息写入数据库
        $user_login_model = UserLoginModel::firstOrCreate([
            'user_id' => $event->user->id
        ]);
        $user_login_model->update([
            'last_login_device' => $user_login_model->login_device,  // 最后一次登录设备
            'last_login_ip' => $user_login_model->login_ip,  // 最后一次登录IP
            'last_login_port' => $user_login_model->login_port,  // 最后一次登录端口号
            'last_login_time' => $user_login_model->login_time,  // 最后一次登录时间
            'login_device' => $device,  // 本次登录设备
            'login_ip' => $ip,  // 本次登录IP
            'login_port' => $port,  // 本次登录端口号
            'login_time' => now(),  // 本次登录时间
        ]);
    }
}
