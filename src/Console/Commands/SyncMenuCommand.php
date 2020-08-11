<?php

namespace System\Console\Commands;

use System\Models\Menu;
use System\Services\MenuService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SyncMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:menu {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync menu database';

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
        $this->line("Start sync menu ...");

        $menus_config_path = config_path('menus.php');
        $menus = include "{$menus_config_path}";

        if ($this->option('force')) {
            //Model::unguard();
            DB::table('menus')->truncate();
            //Model::reguard();
        }

        $menuService = new MenuService();
        $menuService->batchImport($menus);

        // 触发菜单事件
        if ($this->option('force')) {
            event(new \System\Events\Menus\ResetMenuEvent());
        } else {
            event(new \System\Events\Menus\SyncMenuEvent());
        }

        $this->info("Menu sync success.");

        return true;
    }

    private function code()
    {
        \Illuminate\Support\Facades\DB::table('menus')->truncate();
        $menus = config('menus', []);
        $menuService = new \System\Services\MenuService();
        $menuService->batchImport($menus);
    }
}
