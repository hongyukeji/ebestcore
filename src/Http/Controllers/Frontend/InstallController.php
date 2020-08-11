<?php

namespace System\Http\Controllers\Frontend;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use mysqli;
use Illuminate\Http\Request;
use System\Services\SystemService;

class InstallController extends Controller
{
    public function __construct()
    {
        //parent::__construct();

        // 判断系统是否已经安装
        if (get_install_status()) {
            return redirect(url('/'))->send();
        }
    }

    /*
     * 首页
     */
    public function index()
    {
        $step = 'index';
        return view('frontend::install.index', [
            'step' => $step,
        ]);
    }

    /*
     * 检查要求
     */
    public function requirements()
    {
        $step = 'requirements';
        $install = config('install');
        $requirements = [];

        // php 版本
        $requirements['php_version'] = version_compare(PHP_VERSION, $install['requirements']['php_version']) >= 0 ? true : false;

        // php 扩展
        foreach ($install['requirements']['extensions'] as $php_extend) {
            $requirements['extensions'][$php_extend] = extension_loaded($php_extend) ? true : false;
        }

        // disable_functions
        foreach ($install['requirements']['disable_functions'] as $disable_function) {
            $requirements['disable_functions'][$disable_function] = function_exists($disable_function) ? true : false;
        }

        // apache
        /*if (function_exists('apache_get_modules')) {
            foreach ($install['requirements']['apache'] as $apache) {
                $requirements['apache'][$apache] = in_array($apache, apache_get_modules()) ? true : false;
            }
        }*/

        // 文件夹权限检查
        foreach ($install['requirements']['dir_permissions'] as $folder => $permission) {
            $requirements['dir_permissions'][$folder] = substr(sprintf('%o', fileperms(base_path($folder))), -4) >= $permission ? true : false;
        }

        // 是否通过检查
        $check_state = !deep_in_array(false, $requirements);

        return view('frontend::install.requirements', [
            'step' => $step,
            'requirements' => $requirements,
            'check_state' => $check_state,
        ]);
    }

    /*
     * 数据库配置
     */
    public function database()
    {
        $step = 'database';

        return view('frontend::install.database', [
            'step' => $step,
        ]);
    }

    /*
     * 安装
     */
    public function install(Request $request)
    {
        $database = $request->except(['_token']);

        try {
            $mysqli = new mysqli($database['host'], $database['username'], $database['password']);
            // 创建数据库
            $mysqli->query("CREATE DATABASE IF NOT EXISTS " . $database['database'] . " CHARACTER SET " . $database['charset'] . " COLLATE " . $database['collation']);
            // 修改数据库编码
            //$mysqli->query("ALTER DATABASE IF NOT EXISTS " . $database['database'] . " CHARACTER SET " . $database['charset'] . " COLLATE " . $database['collation']);
            // 关闭数据库
            $mysqli->close();
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return redirect()->back()->with('error', "数据库连接失败，请检查所填参数是否正确！[{$error_message}]");
        }
        try {
            // 判断 .env 文件是否存在， 不存在则复制 .env.example 为 .env
            if (!Storage::disk('root')->exists('.env')) {
                Storage::disk('root')->copy('.env.example', '.env');
            }

            // 写入.env配置文件
            set_env([
                'DB_CONNECTION' => $database['driver'],
                'DB_HOST' => $database['host'],
                'DB_PORT' => $database['port'],
                'DB_DATABASE' => $database['database'],
                'DB_USERNAME' => $database['username'],
                'DB_PASSWORD' => $database['password'],
                'DB_CHARSET' => $database['charset'],
                'DB_COLLATION' => $database['collation'],
            ]);
            return redirect(route('frontend.install.setting'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /*
     * 配置视图
     */
    public function settingForm()
    {
        $step = 'setting';
        try {
            // 执行初始化安装命令
            $result = \Illuminate\Support\Facades\Artisan::call('install', ["--force" => true]);
            // 判断安装命令是否执行成功
            if (isset($result) && $result == 0) {
                return view('frontend::install.setting', ['step' => $step,]);
            } else {
                return redirect()->route('frontend.install.database')->with('error', '执行安装命令失败，请联系官方技术人员解决！');
            }
        } catch (\Exception $e) {
            return redirect()->route('frontend.install.database')->with('error', $e->getMessage());
        }
    }

    /*
     * 初始化设置
     */
    public function setting(Request $request)
    {
        // 验证表单信息
        Validator::make($request->all(), [
            'site_name' => 'required|string|max:255',
            'site_title' => 'sometimes|string|max:255|nullable',
            'token' => 'sometimes|string|max:255|nullable',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ], [], [
            'site_name' => '网站名称',
            'site_title' => '网站标题',
            'token' => '授权Token码',
            'username' => '管理员用户名',
            'password' => '管理员密码',
        ])->validate();

        // 设置为生产环境
        set_env([
            'APP_ENV' => 'production',
            'APP_DEBUG' => false,
        ]);

        // 设置网站信息
        settings([
            'websites' => [
                'basic' => array_replace_recursive(config('websites.basic'), [
                    'site_name' => $request->input('site_name'),
                    'site_title' => $request->input('site_title'),
                ])
            ],
            'empower' => [
                'token' => $request->input('empower_token'),
            ],
        ]);

        // 添加超级管理员
        \System\Models\Admin::updateOrCreate([
            'name' => $request->input('username'),
            'password' => bcrypt($request->input('password')),
            'avatar' => config('params.users.default_avatar'),
            'status' => true,
        ])->assignRole(config('systems.security.administrator', 'Administrator'));

        // 开启自动更新
        (new SystemService())->openAutoUpdate();

        return redirect(route('frontend.install.finish'));
    }

    /*
     * 安装完成
     */
    public function finish()
    {
        $step = 'finish';

        // 设置安装信息
        set_install_status();

        return view('frontend::install.finish', [
            'step' => $step,
        ]);
    }
}
