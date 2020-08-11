<?php

namespace System\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use System\Http\Controllers\Backend\Controller;

class ApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => '应用市场',
            'breadcrumbs' => [
                [
                    'name' => '首页',
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => '应用市场',
                    'icon' => '',
                    'link' => '',
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => '刷新',
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.applications.index'),
                ],
            ],
        ];

        $applications = [];
        if (!$request->has('sort')) {
            $request->offsetSet('sort', 'sort');
        }
        if (!$request->has('status')) {
            $request->offsetSet('status', true);
        }
        if (!$request->has('per_page')) {
            $request->offsetSet('per_page', 16);
        }
        if (!empty(config('systems.api_token'))) {
            $request->offsetSet('api_token', config('systems.api_token'));
        }
        try {
            // 发送token至更新服务器
            $http = new \GuzzleHttp\Client;
            $response = $http->get(config('wmt.application_source'), [
                'verify' => false,
                'timeout' => 10,
                'query' => $request->query(),
            ]);
            if ($response->getStatusCode() == 200) {
                $data = json_decode($response->getBody(), true);
                //dump(route_url('api.cloud.applications.index'));
                //dump($data);
                $item = $data['data'];
                $total = $data['meta']['total'];
                $perPage = $data['meta']['per_page'];
                $currentPage = $data['meta']['current_page'];
                $applications = new LengthAwarePaginator($item, $total, $perPage, $currentPage, [
                    'path' => $request->url(),
                    'pageName' => 'page'
                ]);
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }

        return view('backend::applications.index', compact('applications', 'pages'));
    }

    public function download(Request $request)
    {
        $url = $request->input('url');
        $file_url = $url;
        $file_name = basename($file_url);
        $file_path = base_path("storage/app/temps/{$file_name}");
        //$path = dirname($file_path);
        //$$file_info = pathinfo($file_path);
        // 下载文件
        $client = new \GuzzleHttp\Client(['verify' => false]);
        $response = $client->get($file_url);
        if ($response->getStatusCode() == 200) {
            // 下载文件
            $file_zip = $response->getBody()->getContents();
            // 保存文件
            $filesystem = new \Illuminate\Filesystem\Filesystem();
            $filesystem->put($file_path, $file_zip);
            // 解压文件
            $zip = new \ZipArchive();
            $open_res = $zip->open($file_path);
            if ($open_res === true) {
                $zip->extractTo(base_path());
                $zip->close();
            }
            // 删除文件
            if ($filesystem->exists($file_path)) {
                $filesystem->delete($file_path);
            }
        }
        // 返回结果
        return redirect()->back()->with('message', '应用下载成功！');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
