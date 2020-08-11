<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class LogController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.log'),
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
                    'name' => trans('backend.commons.development_management'),
                    'icon' => 'fa fa-circle-o',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.log'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.log.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.log.index'),
                ],
            ],
        ];
        $items = config('log-viewer');
        return view('backend::systems.log.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings(['log-viewer' => array_replace_recursive(config('log-viewer'), $items)]);
        return redirect()->route('backend.systems.log.index')->with('message', trans('backend.messages.update_success'));
    }

    public function clear()
    {
        Artisan::call('clear:log');
        return redirect()->route('backend.systems.log.index')->with('message', '日志数据清除成功');
    }
}
