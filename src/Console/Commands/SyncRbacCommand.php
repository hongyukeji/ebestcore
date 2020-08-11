<?php

namespace System\Console\Commands;

use System\Models\Admin;
use System\Models\Permission;
use System\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SyncRbacCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:rbac {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync rbac database';

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
        $this->line("Start sync rbac ...");
        if ($this->option('force')) {
            Schema::disableForeignKeyConstraints();
            // 清空所有数据表数据
            $tableNames = config('permission.table_names');
            Model::unguard();
            DB::table($tableNames['role_has_permissions'])->delete();
            //DB::table($tableNames['model_has_roles'])->delete();
            //DB::table($tableNames['model_has_permissions'])->delete();
            DB::table($tableNames['roles'])->delete();
            DB::table($tableNames['permissions'])->delete();
            Model::reguard();
            Schema::enableForeignKeyConstraints();
        }

        $rbac = config('rbac');

        // 需清除缓存，否则会报错
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 创建角色
        foreach ($rbac['roles'] as $role) {
            Role::firstOrCreate([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],

            ], [
                'group' => $role['group'],
                'title' => $role['title'],
                'description' => $role['description'],
                'sort' => $role['sort'],
                'status' => $role['status'],
            ]);
        }

        // 创建权限并将权限分配给角色
        foreach ($rbac['permissions'] as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => $permission['guard_name'],
            ], [
                'group' => $permission['group'],
                'title' => $permission['title'],
                'description' => $permission['description'],
                'sort' => $permission['sort'],
                'status' => $permission['status'],
            ])->assignRole($permission['role']);
        }

        // 创建角色并分配权限
        foreach ($rbac['roles'] as $role) {
            Role::firstOrCreate([
                'name' => $role['name'],
                'guard_name' => $role['guard_name'],
            ], [
                'group' => $role['group'],
                'title' => $role['title'],
                'description' => $role['description'],
                'sort' => $role['sort'],
                'status' => $role['status'],
            ])->givePermissionTo($role['permissions'] ?? []);
        }

        $this->info("Rbac sync success.");

        return true;
    }

    public function demo()
    {
        // 需清除缓存，否则会报错
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // 清空所有数据表数据
        $tableNames = config('permission.table_names');
        Model::unguard();
        DB::table($tableNames['role_has_permissions'])->delete();
        DB::table($tableNames['model_has_roles'])->delete();
        DB::table($tableNames['model_has_permissions'])->delete();
        DB::table($tableNames['roles'])->delete();
        DB::table($tableNames['permissions'])->delete();
        Model::reguard();

        // 先创建权限
        Permission::create(['name' => 'manage_systems']);
        Permission::create(['name' => 'manage_users']);
        Permission::create(['name' => 'manage_orders']);

        // 创建站长角色，并赋予权限
        $founder = Role::create(['name' => 'Founder']);
        $founder->givePermissionTo('manage_systems');
        $founder->givePermissionTo('manage_users');
        $founder->givePermissionTo('manage_orders');
    }
}
