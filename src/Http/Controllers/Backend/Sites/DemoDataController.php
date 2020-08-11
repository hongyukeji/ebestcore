<?php

namespace System\Http\Controllers\Backend\Sites;

use System\Http\Controllers\Backend\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DemoDataController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.demo_data')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.website'),
                    'icon' => 'fa fa-sitemap',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.demo_data')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.demo-data.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.demo-data.index'),
                ],
            ],
        ];
        return view('backend::sites.demo-data.index', compact('pages'));
    }

    public function store(Request $request)
    {
        $clear = $request->input('clear', 0);
        if ($clear > 0) {
            DB::table('adverts')->truncate();
            DB::table('navigations')->truncate();
            DB::table('sliders')->truncate();
            DB::table('links')->truncate();
            DB::table('article_categories')->truncate();
            event(new \System\Events\Systems\InitDataEvent());
        }
        if ($clear == 0 || $clear == 2) {
            Artisan::call('sync:demo', ["--force" => true]);
        }
        return redirect()->route('backend.sites.demo-data.index')->with('message', '演示数据更新化成功');
    }
}
