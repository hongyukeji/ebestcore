<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'install {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Application';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //ob_end_clean();   // Artisan 注释掉
        //set_time_limit(0);    //设置超时时间
        //ini_set('memory_limit', '10240M');
        //ini_set('memory_limit', '-1');    // 内存无限
        //ini_set('max_execution_time', '0');// 设置永不超时，无限执行下去直到结束

        if (Storage::disk('local')->exists('installer/install.json')) {
            $this->warn("System installed.");
            return;
        }

        $this->line("Start install command...");

        $force = $this->option('force');

        //$this->call('optimize:clear');

        // 重新生成缓存包清单
        //$this->call('package:discover');

        // Composer 自动加载
        $this->call('composer:autoload');

        // 生成 Application 密钥
        $this->call('key:generate', ["--force" => $force]);

        // 生成 jwt 密钥
        $this->call('jwt:secret', ["--force" => $force]);

        // 数据库回滚迁移
        $this->call('migrate:fresh', ["--seed" => $force, "--force" => $force]);

        // 清除缓存文件
        $this->call('clear:cache', ["--force" => $force]);

        // 同步 RBAC 权限至数据库
        $this->call('sync:rbac', ["--force" => $force]);

        // 同步 Menus 菜单至数据库
        $this->call('sync:menu', ["--force" => $force]);

        // 同步演示数据至数据库
        $this->call('sync:demo', ["--force" => $force]);

        // 系统 - 安装事件
        event(new \System\Events\Systems\InstallEvent());

        $this->info("Install success.");
    }

}
