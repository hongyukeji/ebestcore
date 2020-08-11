<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QueueController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.queue'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.system'),
                    'icon' => 'fa fa-gears',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.queue'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.queue.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.queue.index'),
                ],
            ],
        ];
        $items = config('queue');
        $queue_count = [
            'wait' => DB::table('jobs')->count(),
            'fail' => DB::table('failed_jobs')->count(),
        ];
        $queue_count['total'] = $queue_count['wait'] + $queue_count['fail'];
        return view('backend::systems.queue.index', compact('items', 'queue_count', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['queue' => array_replace_recursive(config('queue'), $items)]);
        return redirect()->route('backend.systems.queue.index')->with('message', trans('backend.messages.update_success'));
    }

    public function work()
    {
        try {
            if (!function_exists('exec')) {
                return redirect()->route('backend.systems.queue.index')->with('warning', 'exec() 函数不可用，请在php.ini中开启 exec 函数');
            }
            if (!function_exists('popen')) {
                return redirect()->route('backend.systems.queue.index')->with('warning', 'popen() 函数不可用，请在php.ini中开启 popen 函数');
            }
            if (!function_exists('pclose')) {
                return redirect()->route('backend.systems.queue.index')->with('warning', 'pclose() 函数不可用，请在php.ini中开启 pclose 函数');
            }
            $php = php_run_path();
            if (strpos(strtolower(PHP_OS), 'win') === 0) {
                $cmd = "chcp 65001 && cd " . base_path() . " && start /b {$php} artisan queue:work > storage/logs/queue-work-output.log";
                pclose(popen($cmd, "r"));
            } else {
                $cmd = "cd " . base_path() . " && nohup {$php} artisan queue:work > storage/logs/queue-work-output.log 2>&1 &";
                pclose(popen($cmd, "r"));
            }
            return redirect()->route('backend.systems.queue.index')->with('message', '运行队列成功');
        } catch (\Exception $e) {
            return redirect()->route('backend.systems.queue.index')->with('warning', $e->getMessage());
        }
    }

    public function clear()
    {
        Artisan::call('queue:flush');
        return redirect()->route('backend.systems.queue.index')->with('message', '清空队列所有失败任务成功');
    }

    public function restart()
    {
        Artisan::call('queue:restart');
        return redirect()->route('backend.systems.queue.index')->with('message', '重启所有队列任务成功');
    }

    public function retry()
    {
        Artisan::call('queue:retry all');
        return redirect()->route('backend.systems.queue.index')->with('message', '重新执行所有失败任务成功');
    }
}
