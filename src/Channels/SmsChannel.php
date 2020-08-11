<?php

namespace System\Channels;

use Illuminate\Notifications\Notification;

class SmsChannel
{
    /**
     * 发送指定的通知.
     *
     * @param mixed $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $result = $notification->toSms($notifiable);

        // Send notification to the $notifiable instance...
        loggers('短信频道：', $result);
        return $result;
    }
}
