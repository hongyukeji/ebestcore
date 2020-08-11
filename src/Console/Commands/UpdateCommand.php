<?php

namespace System\Console\Commands;

use Illuminate\Console\Command;

class UpdateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Application';

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
        $this->line("Start update command...");

        // 清理排除列表
        $migrations = [
            'cloud_agents', 'cloud_empowers', 'cloud_plugins', 'cloud_update_logs', 'cloud_update_notices', 'cloud_updates',
            'admins',
            'users', 'user_accounts', 'user_addresses', 'user_extends', 'user_grades', 'user_logins', 'user_oauths',
            'settings', 'permission',
            'articles', 'article_categories', 'article_comments',
            'regions',
        ];

        // 清理迁移数据库记录
        $this->call('clear:migration', ["migrations" => $migrations, "--option" => 'exclude']);

        // 清理数据库表
        $tables = [
            'cloud_agents', 'cloud_empowers', 'cloud_plugins', 'cloud_update_logs', 'cloud_update_notices', 'cloud_updates',
            'admins',
            'users', 'user_accounts', 'user_addresses', 'user_extends', 'user_grades', 'user_logins', 'user_oauths',
            'settings', 'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
            'articles', 'article_categories', 'article_comments',
            'regions',
        ];
        $this->call('clear:database', ["tables" => $tables, "--option" => 'exclude']);

        // Composer 自动加载
        $this->call('composer:autoload');

        // 生成 Application 密钥
        //$this->call('key:generate', ["--force" => true]);

        // 生成 jwt 密钥
        //$this->call('jwt:secret', ["--force" => true]);

        // 执行数据库迁移命令
        $this->call('migrate', ["--force" => true]);

        // 同步 RBAC 权限至数据库
        $this->call('sync:rbac', ["--force" => true]);

        // 同步 Menus 菜单至数据库
        $this->call('sync:menu', ["--force" => true]);

        // 同步演示数据至数据库
        $this->call('sync:demo', ["--force" => true]);

        // 清除缓存文件
        $this->call('clear:cache', ["--force" => true]);

        // 系统 - 更新事件
        event(new \System\Events\Systems\UpdateEvent());

        $this->info("Update success.");
    }

}
