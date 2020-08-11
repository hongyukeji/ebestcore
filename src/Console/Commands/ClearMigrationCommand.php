<?php

namespace System\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Illuminate\Console\Command;

class ClearMigrationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:migration {migrations?*} {--option=exclude}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled migration files';

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

        try {
            $migrations = $this->argument('migrations');
            // 迁移数据库表中名称
            $migrations = array_map(function ($migration) {
                return "%{$migration}%";
            }, $migrations);
            $query = DB::table('migrations');
            foreach ($migrations as $migration) {
                $query->orWhere('migration', 'like', $migration);
            }
            $where_migrations = $query->get()->pluck('migration');
            $option = $this->option('option');
            if ($option == 'exclude') {
                DB::table('migrations')->whereNotIn('migration', $where_migrations)->delete();
            } else {
                if (isset($migrations) && count($migrations)) {
                    DB::table('migrations')->whereIn('migration', $where_migrations)->delete();
                } else {
                    DB::table('migrations')->delete();
                }
            }
        } catch (\Exception $e) {
            Log::warning('[清除数据库表迁移记录]' . $e->getMessage());
        }

        $this->info('Compiled migration cleared.');
    }

}
