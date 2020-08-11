<?php

namespace System\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * 队列的名称。
     *
     * @var string|null
     */
    //public $queue = 'emails';

    /**
     * 任务最大尝试次数。
     *
     * @var int
     */
    public $tries = 3;

    /**
     * 任务运行的超时时间。
     *
     * @var int
     */
    public $timeout = 30;

    /**
     * 邮箱
     *
     * @var
     */
    protected $email;

    /**
     * 内容
     *
     * @var string
     */
    protected $content;

    /**
     * Create a new job instance.
     *
     * @param $email
     * @param string $content
     */
    public function __construct($email, $content = '')
    {
        $this->email = $email;
        $this->content = $content;
    }

    /**
     * 执行任务
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::raw($this->content, function ($message) {
                $message->to($this->email);
            });
            Log::info("发送邮件到 -> " . $this->email . " 邮件内容：" . $this->content);
        } catch (\Exception $e) {
            Log::warning("[" . get_class() . "]" . $e->getMessage());
        }
    }

    /**
     * 任务失败的处理过程
     *
     * @param Exception $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        // 给用户发送任务失败的通知，等等……
    }
}
