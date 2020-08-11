<?php

namespace System\Http\Controllers\Backend\Systems;

use Hongyukeji\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class RoutesController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.route')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.route')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.routes.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.routes.index'),
                ],
            ],
        ];

        $items = config('systems.routes');

        return view('backend::systems.routes.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        settings([
            'systems' => [
                'routes' => array_replace_recursive(config('systems.routes'), $items),
            ]
        ]);
        (new \System\Services\CacheService())->forgetGroup(\System\Services\CacheService::CACHE_GROUP_SYSTEM);
        return redirect()->route('backend.systems.routes.index')->with('message', trans('backend.messages.update_success'));
    }

    public function init()
    {
        $systems = config('systems');
        unset($systems['routes']);
        settings(['systems' => $systems]);
        (new \System\Services\CacheService())->forgetGroup(\System\Services\CacheService::CACHE_GROUP_SYSTEM);
        return redirect()->route('backend.systems.routes.index')->with('message', '初始化路由配置成功！');
    }
}
