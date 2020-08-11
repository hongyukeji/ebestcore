<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use System\Services\SystemService;

class UpdateController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.online_update'),
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
                    'name' => trans('backend.commons.online_update'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.update.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.update.index'),
                ],
            ],
        ];

        // 环境检测
        $installer = config('install');
        $requirements = [];
        // php 版本
        $requirements['php_version'] = version_compare(PHP_VERSION, $installer['requirements']['php_version']) >= 0 ? true : false;
        // php 扩展
        foreach ($installer['requirements']['extensions'] as $php_extend) {
            $requirements['extensions'][$php_extend] = extension_loaded($php_extend) ? true : false;
        }
        // disable_functions
        foreach ($installer['requirements']['disable_functions'] as $disable_function) {
            $requirements['disable_functions'][$disable_function] = function_exists($disable_function) ? true : false;
        }
        // 文件夹权限检查
        foreach ($installer['requirements']['dir_permissions'] as $folder => $permission) {
            $requirements['dir_permissions'][$folder] = substr(sprintf('%o', fileperms(base_path($folder))), -4) >= $permission ? true : false;
        }
        // 文件可写权限检查
        foreach ($installer['requirements']['write_files'] as $file => $permission) {
            $requirements['write_files'][$file] = is_writable(realpath(base_path($file))) ? true : false;
        }
        // 是否通过检查
        //$check_state = !deep_in_array(false, $requirements);
        // 更新
        $app_update = [
            'update_source' => config('wmt.update_source'),
            'update_stability' => config('app.update_stability'),
            'update_auto' => config('app.update_auto'),
        ];

        return view('backend::systems.update.index', compact('requirements', 'app_update', 'pages'));
    }

    public function system()
    {
        ini_set('memory_limit', '-1');  // -1 取消内存限制
        ini_set('max_execution_time', '0');
        ignore_user_abort(true);    // 关掉浏览器，PHP脚本也可以继续执行
        set_time_limit(0);  // 通过set_time_limit(0)可以让程序无限制的执行下去
        $systemService = new SystemService();
        return $systemService->update();
    }

    public function websiteOpen()
    {
        if (!file_exists(storage_path('framework/down'))) {
            $exitCode = Artisan::call('down');
            // 判断命令是否执行成功
            if ($exitCode == 0) {
                return back()->with('success', '网站开启维护模式成功');
            } else {
                return back()->with('error', '网站开启维护模式失败');
            }
        } else {
            return back()->with('info', '网站已经是维护模式');
        }
    }

    public function websiteClose()
    {
        if (file_exists(storage_path('framework/down'))) {
            $exitCode = Artisan::call('up');
            // 判断命令是否执行成功
            if ($exitCode == 0) {
                return back()->with('success', '网站关闭维护模式成功');
            } else {
                return back()->with('error', '网站关闭维护模式失败');
            }
        } else {
            return back()->with('info', '网站已经关闭维护模式');
        }
    }

    public function releaseNote()
    {
        $pages = [
            'title' => trans('backend.commons.update_log'),
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
                    'name' => trans('backend.commons.update_log'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.update.release-note'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.update.release-note'),
                ],
            ],
        ];
        try {
            $file_release_note = file_get_contents(base_path('release-note.md'));
        } catch (\Exception $e) {
            $file_release_note = '# ' . config('app.name') . ' - 更新日志';
        }
        $release_note_json = json_encode($file_release_note, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $markdown = trim($release_note_json, '"');
        return view('backend::systems.update.release-note', compact('markdown', 'pages'));
    }

    /*
     * 开启自动更新
     */
    public function autoUpdate(Request $request)
    {
        if ((new SystemService())->openAutoUpdate()) {
            return back()->with('success', '自动更新设置成功');
        } else {
            return back()->with('fail', '自动更新设置失败');
        }
    }

    private function code()
    {
        //
    }
}
