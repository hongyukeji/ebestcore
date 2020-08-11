<?php

namespace System\Console\Commands;

use System\Models\Admin;
use Illuminate\Console\Command;

class SyncSuperAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:super-admin {name} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync super admin.';

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
        $name = $this->argument('name');
        $password = $this->argument('password');

        // 添加超级管理员
        Admin::updateOrCreate([
            'name' => $name,
        ], [
            'password' => bcrypt($password),
            'status' => true,
        ])->assignRole(config('systems.security.administrator', 'Administrator'));

        $this->info('[' . $name . ']用户更新成功！');
    }
}
