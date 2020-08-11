<?php

namespace System\Providers;

use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // 注册系统命令
        $this->commands([
            //\System\Console\Commands\ExampleCommand::class,
            \System\Console\Commands\ClearBootstrapCommand::class,
            \System\Console\Commands\ClearCacheCommand::class,
            \System\Console\Commands\ClearLogCommand::class,
            \System\Console\Commands\ClearSessionCommand::class,
            \System\Console\Commands\ClearStorageCommand::class,
            \System\Console\Commands\ClearSystemCommand::class,
            \System\Console\Commands\ComposerAutoloadCommand::class,
            \System\Console\Commands\InstallCommand::class,
            \System\Console\Commands\SyncDemoCommand::class,
            \System\Console\Commands\SyncMenuCommand::class,
            \System\Console\Commands\SyncRbacCommand::class,
            \System\Console\Commands\SyncSuperAdminCommand::class,
            \System\Console\Commands\UninstallCommand::class,
            \System\Console\Commands\DbBackupCommand::class,
            \System\Console\Commands\ClearDatabaseCommand::class,
            \System\Console\Commands\ClearMigrationCommand::class,
        ]);
    }
}
