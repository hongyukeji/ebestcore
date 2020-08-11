<?php

namespace System\Console\Commands;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Illuminate\Console\Command;

class ClearDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:database {tables?*} {--option=exclude}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all compiled database files';

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
            // 关闭数据库外键约束
            Schema::disableForeignKeyConstraints();
            // 所有数据库表
            $all_tables = DB::connection()->getDoctrineSchemaManager()->listTableNames();
            $tables = $this->argument('tables');
            $option = $this->option('option');
            if ($option == 'exclude') {
                array_push($tables, 'migrations');
                $clear_tables = array_diff($all_tables, $tables);
                $str_tables = implode(',', $clear_tables);
            } else {
                //array_splice($tables, array_search('migrations', $tables), 1);
                $str_tables = implode(',', $tables);
            }
            if (!empty($str_tables)) {
                DB::statement("DROP TABLE IF EXISTS {$str_tables}");
            }
            // 开启数据库外键约束
            Schema::enableForeignKeyConstraints();
        } catch (\Exception $e) {
            Log::warning('[清除数据库]' . $e->getMessage());
        }

        $this->info('Compiled database cleared.');
    }

}
