<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Webpatser\Uuid\Uuid;

if (!function_exists('result')) {
    /*
     * 统一结果返回响应
     *
     * 返回格式示例: $result = result()->success('example');
     *
     * 验证返回数据: echo result()->verify($result);
     */
    function result()
    {
        return new \System\Librarys\Supports\Result();
    }
}

if (!function_exists('get_install_status')) {
    /*
     * 判断系统是否已经安装
     */
    function get_install_status()
    {
        $install_status = Storage::disk('local')->exists('install/install.json') ? true : false;
        return $install_status;
    }
}

if (!function_exists('set_install_status')) {
    /*
     * 设置安装状态
     */
    function set_install_status()
    {
        $file_path = 'installer/install.json';
        try {
            $installer = [
                'domain' => get_server_url(),
                'token' => config('empower.token'),
                'install_date' => date('Y-m-d H:i:s'),
            ];
            $json_string = json_encode($installer, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            Storage::disk('local')->put($file_path, $json_string);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('delete_install_status')) {
    /*
     * 删除安装状态
     */
    function delete_install_status()
    {
        try {
            $file_path = 'installer/install.json';
            if (Storage::disk('local')->exists($file_path)) {
                Storage::disk('local')->delete($file_path);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

if (!function_exists('collects')) {
    /**
     * Create a collection from the given value.
     *
     * @param mixed $value
     * @return \System\Librarys\Supports\Collection
     */
    function collects($value = null)
    {
        return new \System\Librarys\Supports\Collection($value);
    }
}

if (!function_exists('uuid')) {
    function uuid()
    {
        return $uuid = UUID::generate()->hex;
    }
}

if (!function_exists('route_class')) {
    function route_class()
    {
        return str_replace('.', '-', Route::currentRouteName());
    }
}

if (!function_exists('envs')) {
    /*
     * 增加env()获取值为空判断
     */
    function envs($key, $default = null)
    {
        return empty(env($key)) ? $default : env($key);
    }
}

if (!function_exists('get_current_action')) {
    /*
     * 获取当前控制器与方法
     */
    function get_current_action($key = null)
    {
        $action = Route::current()->getActionName();
        list($class, $method) = explode('@', $action);

        switch ($key) {
            case "controller":
                return $class;
                break;
            case "method":
                return $method;
                break;
            default:
                return ['controller' => $class, 'method' => $method];
        }
    }
}

if (!function_exists('is_active_url')) {
    function is_active_url($url, $output = 'active')
    {
        // 完整网址
        $url_full = URL::full();

        // 存在参数, 则截取?问号之前的网址
        $url_trim = Str::contains($url_full, '?') ? Str::before($url_full, '?') : $url_full;

        if ($url_trim == $url) {
            return $output;
        } else if ($url_trim == str_start($url, get_server_url())) {
            return $output;
        }

        return '';
    }
}

if (!function_exists('is_active_route')) {
    function is_active_route($route, $output = 'active')
    {
        if (Route::currentRouteName() == $route) {
            return $output;
        }
    }
}

if (!function_exists('route_url')) {
    /**
     * 自动获取route或url
     *
     * @param $name
     * @param array $parameters
     * @param bool $absolute
     * @return mixed
     */
    function route_url($name, $parameters = [], $absolute = true)
    {
        if (Route::has($name)) {
            return route($name, $parameters, $absolute);
        } else {
            $url = $name;
            if (Str::startsWith($name, ['/'])) {
                $app_url = config('app.url');
                $url_prefix = Str::endsWith($app_url, '/') ? Str::beforeLast($app_url, '/') : $app_url;
                $url = !empty(Str::after($name, '/')) ? $url_prefix . $name : $url_prefix;
            }
            if (!empty($parameters) && is_array($parameters)) {
                $url = Str::finish($url, '?') . http_build_query($parameters);
            } else if (!empty($parameters)) {
                $url = Str::finish($url, '/') . $parameters;
            }
            return $url;
        }
    }
}

if (!function_exists('has_route')) {
    /**
     * 自动获取route或url
     *
     * @param $name
     * @return mixed
     */
    function has_route($name)
    {
        return Route::has($name);
    }
}

if (!function_exists('set_env')) {
    /**
     * 修改系统 .env 文件
     *
     * @param array $data
     * @return bool
     */
    function set_env(array $data)
    {
        $envPath = base_path('.env');
        $contentCollect = collect(file($envPath, FILE_IGNORE_NEW_LINES));
        $contentCollect->transform(function ($item) use (&$data) {
            foreach ($data as $key => $value) {
                if (starts_with($item, $key)) {
                    $env = $key . '=' . $value;
                    unset($data[$key]);
                    return $env;
                }
            }
            return $item;
        });
        $contentArray = $contentCollect->toArray();
        // 新增env
        foreach ($data as $k => $v) {
            array_push($contentArray, $k . '=' . $v);
        }
        $content = implode($contentArray, "\n");
        file_put_contents($envPath, $content);
        return true;
    }
}

if (!function_exists('assets')) {
    /**
     * Generate an asset path for the application.
     *
     * @param string $path
     * @param bool|null $secure
     * @return string
     */
    function assets($path, $secure = null)
    {
        if (Str::endsWith($path, '.css') || Str::endsWith($path, '.js')) {
            return app('url')->asset($path . '?v=' . config('app.version'), $secure);
        } else {
            return app('url')->asset($path, $secure);
        }
    }
}

if (!function_exists('asset_url')) {
    /**
     * 获取系统存储盘对应路径 url
     *
     * @param $path
     * @param array $options
     * @return mixed
     */
    function asset_url($path, $options = [])
    {
        if (empty($path)) {
            return null;
        }
        switch ($path) {
            case starts_with($path, ['/']):
                return get_server_url() . $path;
                break;
            case starts_with($path, ['http://', 'https://', 'ftp://', 'data:image/']):
                return $path;
                break;
            case Storage::exists($path):
                try {
                    if ($options['temp_file'] && method_exists(Storage::class, 'temporaryUrl')) {
                        return Storage::temporaryUrl($path, now()->addSeconds($options['expires_in'] ?? 60 * 15));
                    }
                } catch (Exception $e) {
                    //
                }
                return Storage::url($path);
                break;
            default:
                return $path;
        }
    }
}

if (!function_exists('generate_key')) {
    /*
     * 生成密钥
     */
    function generate_key($cipher = 'AES-256-CBC')
    {
        return random_bytes($cipher == 'AES-128-CBC' ? 16 : 32);
    }
}

if (!function_exists('generate_random_key')) {
    /*
     * 生成随机密钥
     */
    function generate_random_key($cipher = '')
    {
        return 'base64:' . base64_encode(random_bytes((!empty($cipher) ? $cipher : config('app.cipher', 'AES-256-CBC')) == 'AES-128-CBC' ? 16 : 32));
    }
}

if (!function_exists('number_format_unit')) {
    function number_format_unit($value, $prefix = '', $suffix = '', $prec = 2)
    {
        $int = null;
        $dec = null;
        $targ = null;
        $number = null;
        $times = [
            '',
            '万',
            '亿',
            '万亿',
            '亿亿',
        ];
        if (!preg_match('/^-?\d+\.?\d+$/', $value)) {
            return 0;
        }
        if (starts_with($value, '-')) {
            $targ = '-';
            $number = str_after($value, '-');
        } else {
            $number = $value;
        }
        $times_num = 0;
        while ($number > 10000) {
            $number = $number / 10000;
            $times_num++;
        }
        $new_number = round($number, $prec);
        if (strpos($new_number, '.')) {
            $array_number = explode('.', $new_number);
            $int = $array_number[0];
            $dec = $array_number[1];
            /*if (strlen($int) > 3) {
                // obj.int = obj.int.replace(/(.{1})/, '$1,')
                $int = $int;
            }*/
            return $prefix . $targ . $int . '.' . $dec . $times[$times_num] . $suffix;
        } else {
            return $prefix . $targ . $new_number . $times[$times_num] . $suffix;
        }
    }

}

if (!function_exists('loggers')) {
    /*
     * 日志记录
     */
    function loggers($message = null, $context = null)
    {
        if (is_array($context)) {
            logger($message, $context);
        } elseif (is_object($context)) {
            logger($message, get_object_vars($context));
        } else {
            logger($message, collect($context)->toArray());
        }
    }
}

if (!function_exists('api_result')) {
    /**
     * Result Api json 数据格式返回标准
     *
     * Api接口响应状态码，大于等于200小于300表示成功；大于等于400小于500为客户端错误；大于500为服务端错误。
     *
     * @param $status_code
     * @param null $message
     * @param null $data
     * @return array
     */
    function api_result($status_code = 0, $message = null, $data = null)
    {
        $status = config('statuscode', [
            '0' => "Success",   // 成功
            '1' => "Fail",  // 失败
            '-1' => "System busy",  // 系统繁忙
        ]);

        try {
            $result = [
                'status_code' => $status_code,
                'message' => $message ?: (isset($status[$status_code]) ? $status[$status_code] : 'unknown'),
            ];
            if (isset($data)) {
                $result['data'] = $data;
            }
            return $result;
        } catch (Exception $e) {
            return [
                'status_code' => 1,
                'message' => $e->getMessage(),
            ];
        }
    }
}

if (!function_exists('uploads_path')) {
    /*
     * 文件上传路径
     */
    function uploads_path($option, $time_stamp = 'Y/m/d')
    {
        $path = config("uploads.paths.{$option}", 'uploads/defaults');
        if ($time_stamp) {
            return Str::finish($path, '/') . date($time_stamp, time());
        } else {
            return $path;
        }
    }
}

if (!function_exists('get_mysql_server_info')) {
    /**
     * 获取MySQL数据库版本
     *
     * @return string
     */
    function get_mysql_server_info()
    {
        try {
            $host = config('database.connections.mysql.host');
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $mysqli = new mysqli(config('database.connections.mysql.host'), config('database.connections.mysql.username'), config('database.connections.mysql.password'));
            return $mysqli->server_info;
        } catch (Exception $e) {
            return '';
        }
    }
}

if (!function_exists('check_version')) {
    /*
     * 检查版本号
     *
     * [ true => 有更新, false => 没有更新 ]
     * 返回值: true => 表示传入的参数版本号大于系统当前版本号, false => 表示传入的参数版本号等于或者小于当前版本号
     */
    function check_version($now_version, $update_version)
    {
        return version_compare($update_version, $now_version, '>');
    }
}

if (!function_exists('get_storage_disk')) {
    /*
     * 获取存储磁盘信息
     */
    function get_storage_disk($key = null, $value = null)
    {
        $default_disk = config("filesystems.default");
        $disk = config("filesystems.disks.{$default_disk}");
        if ($key) {
            if (isset($disk[$key])) {
                return $disk[$key];
            } else {
                return $value;
            }
        } else {
            return $disk;
        }
    }
}

if (!function_exists('get_app_version')) {
    /*
     * 获取系统版本号
     */
    function get_app_version()
    {
        return str_before(config('app.version'), 'v');
    }
}

if (!function_exists('get_site_name')) {
    /*
     * 获取网站名称
     */
    function get_site_name()
    {
        return config('websites.basic.site_name', config('app.name'));
    }
}

if (!function_exists('get_app_locales')) {
    /*
     * 获取系统语言列表
     */
    function get_app_locales()
    {
        return get_template_dir(base_path('resources/lang'));
    }
}

if (!function_exists('get_system_empower_status')) {
    /*
     * 获取系统授权状态
     */
    function get_system_empower_status()
    {
        $seconds = 60 * 60 * 24 * 7;
        return Cache::remember('wmt_system_empower_status', $seconds, function () {
            return query_system_empower_status();
        });
    }
}

if (!function_exists('query_system_empower_status')) {
    /*
     * 查询系统授权状态
     */
    function query_system_empower_status()
    {
        $empower_status = false;

        try {
            $query_url = config('empower.api_query_url');

            $client = new \GuzzleHttp\Client();
            $response = $client->get($query_url, [
                'verify' => false,  // 禁用SSL证书验证
                'timeout' => 30,  // 超时
                'query' => [
                    'domain' => get_server_url(),
                ],
            ]);
            if ($response->getStatusCode() == 200) {
                $result = json_decode($response->getBody(), true);
                if (is_array($result) && isset($result['data']['empower_status'])) {
                    $empower_status = boolval($result['data']['empower_status']);
                }
            }
            return $empower_status;
        } catch (Exception $e) {
        }

        return $empower_status;
    }
}

if (!function_exists('plugin_version_require')) {
    /*
     * 插件版本号依赖检查
     */
    function plugin_version_require($require_version, $current_version = null)
    {
        if (empty($require_version)) {
            return true;
        }
        if (empty($current_version)) {
            $current_version = get_app_version();
        }
        return version_require($current_version, $require_version);
    }
}

if (!function_exists('time_format_second')) {
    /*
     * 处理时间
     */
    function time_format_second($seconds)
    {
        // $s = gmdate('H:i:s', $seconds);
        $seconds = (int)$seconds;
        if ($seconds > 3600) {
            if ($seconds > 24 * 3600) {
                $days = (int)($seconds / 86400);
                $days_num = $days . "天";
                $seconds = $seconds % 86400;//取余
            }
            $hours = intval($seconds / 3600);
            $minutes = $seconds % 3600;//取余下秒数
            if (isset($days_num)) {
                $time = $days_num . $hours . "小时" . gmstrftime('%M分钟%S秒', $minutes);
            } else {
                $time = $hours . "小时" . gmstrftime('%M分钟%S秒', $minutes);
            }
        } else {
            $time = gmstrftime('%H小时%M分钟%S秒', $seconds);
        }
        return $time;
    }
}

if (!function_exists('get_payment_time_remaining')) {
    /*
     * 获取支付剩余时间
     */
    function get_payment_time_remaining($time)
    {
        $payment_ttl = config('params.orders.payment_ttl', 60 * 60 * 12);
        return now()->diffForHumans(now()->parse($time)->addSeconds(intval($payment_ttl)), true);
    }
}

if (!function_exists('get_template_dir')) {
    /**
     * 获取给定路径下的所有目录
     *
     * @param $template_path
     * @param array $excludes
     * @return array
     */
    function get_template_dir($template_path, array $excludes = [])
    {
        if (is_dir($template_path)) {
            $filesystem = new \Illuminate\Filesystem\Filesystem();
            $directories = $filesystem->directories($template_path);
            foreach ($directories as $key => $directory) {
                $directories[$key] = str_after($directory, $template_path . DIRECTORY_SEPARATOR);
                if (str_contains($directory, $excludes)) {
                    unset($directories[$key]);
                }
            }
            return $directories;
        } else {
            return [];
        }

    }
}
