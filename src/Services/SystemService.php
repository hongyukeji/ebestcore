<?php

namespace System\Services;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class SystemService extends Service
{
    public function update()
    {
        ini_set('memory_limit', '-1');  // -1 取消内存限制
        ini_set('max_execution_time', '0');
        ignore_user_abort(true);    // 关掉浏览器，PHP脚本也可以继续执行
        set_time_limit(0);  // 通过set_time_limit(0)可以让程序无限制的执行下去

        try {
            /*if (!function_exists('beast_encode_file')) {
                return redirect()->back()->with('warning', '系统内核升级，请联系官方技术人员进行服务器环境重新部署');
            }*/

            // 判断exec执行函数是否可用
            if (!function_exists('exec')) {
                return redirect()->back()->with('warning', 'PHP exec()函数未开启，请在php.ini文件中关闭 安全模式 safe_mode = off，disable_functions中删除exec，然后重启或重载配置php服务');
            }

            // 请求参数
            $domain = get_server_url();
            $update_url = config('wmt.update_source');
            $current_version = config('app.version');
            $update_stability = config('app.update_stability');

            // 发起更新请求
            try {
                $client = new \GuzzleHttp\Client();
                $res = $client->request('GET', $update_url, [
                    'verify' => false,
                    'timeout' => 10,
                    'query' => [
                        'domain' => $domain,
                        'version' => $current_version,
                        'version_type' => $update_stability,
                    ],
                ]);
            } catch (GuzzleException $e) {
                return redirect()->back()->with('error', '更新服务器没有响应，请稍后重试！');   // $e->getMessage()
            }

            // 判断是否请求成功
            if ($res->getStatusCode() !== 200) {
                return redirect()->back()->with('error', '在线更新服务器错误');
            }

            // 获取服务器返回json格式更新数据
            $result_update_data = json_decode($res->getBody(), true);
            if ($result_update_data['status_code'] !== 0) {
                return redirect()->back()->with('fail', $result_update_data['message'] ?? '更新服务器没有响应，请稍后重试！');
            }
            $update_data = $result_update_data['data'];

            // 检查版本
            if (!check_version($current_version, $update_data['latest_version'])) {
                return redirect()->back()->with('message', '您的系统当前已是最新版本');
            }

            // 判断是否存在系统更新
            if (isset($update_data['updates']) && count($update_data['updates'])) {
                $codes = [];
                foreach ($update_data['updates'] as $update) {
                    // 更新包
                    if (!empty($update['file_url'])) {
                        try {
                            $file_url = $update['file_url'];
                            $base_path = 'storage/app/update';
                            $path = base_path($base_path);
                            mk_folder($path);
                            $file_name = basename($file_url);
                            $file_path = str_finish($path, '/') . $file_name;
                            $out_path = base_path();
                            $client = new \GuzzleHttp\Client(['verify' => false]);
                            $response = $client->get($file_url);
                            if ($response->getStatusCode() == 200) {
                                // 下载文件
                                $file_zip = $response->getBody()->getContents();
                                $filesystem = new \Illuminate\Filesystem\Filesystem();
                                $filesystem->put($file_path, $file_zip);
                                // 解压文件
                                $zip = new \ZipArchive();
                                $open_res = $zip->open($file_path);
                                if ($open_res === true) {
                                    $zip->extractTo($out_path);
                                    $zip->close();
                                }
                                // 删除文件
                                if ($filesystem->exists($file_path)) {
                                    $filesystem->delete($file_path);
                                }
                                // 清空文件夹
                                //$filesystem->cleanDirectory($path);
                            }
                        } catch (\Exception $e) {
                            Log::warning('[系统更新]' . $e->getMessage());
                            return back()->with('error', $e->getMessage());
                        }
                    }

                    // 代码
                    if (!empty($update['code'])) {
                        array_pull($codes, $update['code']);
                    }

                    // 更新系统版本号
                    settings(['app' => array_replace_recursive(config('app'), ['version' => $update['version']])]);
                }

                // 系统 - 更新事件
                event(new \System\Events\Systems\UpdateEvent());

                // 循环执行命令
                foreach (array_unique($codes) as $code) {
                    try {
                        @eval(gzinflate(base64_decode($code)));
                    } catch (\Exception $e) {
                        Log::warning('[系统更新]' . $e->getMessage());
                        return back()->with('error', $e->getMessage());
                    }
                }
            } else {
                // 存在更新版本，无zip更新包
                return back()->with('message', '您的系统已是最新版本');
            }
            return redirect()->back()->with('success', '恭喜您！系统更新成功');
        } catch (\Exception $e) {
            Log::warning('[系统更新]' . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function openAutoUpdate()
    {
        $request = request();
        $update_source = $request->input('update_source', config('wmt.update_source'));
        $update_notice_url = $request->input('update_notice_url') ? $request->input('update_notice_url') : route_url('api.v1.system.auto-update.index');
        $update_token = $request->input('update_token', config('app.update_token') ?: strtoupper(uuid()));
        $update_stability = $request->input('update_stability', '1');
        $update_auto = $request->input('update_auto', true);

        $update = [
            'update_source' => $update_source,
            'update_notice_url' => $update_notice_url,
            'update_stability' => $update_stability,
            'update_auto' => $update_auto,
            'update_token' => $update_token,
        ];

        settings(['app' => array_replace_recursive(config('app'), $update)]);

        try {
            // 发送token至更新服务器
            $http = new \GuzzleHttp\Client;
            $response = $http->post($update_source, [
                'verify' => false,
                'timeout' => 30,
                'form_params' => [
                    'update_notice_url' => $update_notice_url,
                    'update_token' => $update_token,
                    'update_auto' => $update_auto,
                ],
            ]);
            return true;
        } catch (\Exception $e) {
            Log::warning('[提交系统自动更新信息出错]' . $e->getMessage());
            return false;
        }
    }

}
