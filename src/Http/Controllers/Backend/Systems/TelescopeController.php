<?php

namespace System\Http\Controllers\Backend\Systems;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use System\Http\Controllers\Backend\Controller;

class TelescopeController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.telescope'),
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
                    'name' => trans('backend.commons.telescope'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.telescope.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.telescope.index'),
                ],
            ],
        ];
        $items = config('telescope');
        return view('backend::systems.telescope.index', compact('items', 'pages'));
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');
        $items['ignore_paths'] = explode('#', str_replace(array("\r\n", "\r", "\n"), '#', $items['ignore_paths']));
        $items['ignore_commands'] = explode('#', str_replace(array("\r\n", "\r", "\n"), '#', $items['ignore_commands']));
        settings(['telescope' => array_replace_recursive(config('telescope'), $items)]);
        return redirect()->route('backend.systems.telescope.index')->with('message', trans('backend.messages.update_success'));
    }

    public function clear()
    {
        Artisan::call('telescope:clear');
        return redirect()->route('backend.systems.telescope.index')->with('message', 'Telescope 数据清除成功');
    }

    public function prune()
    {
        Artisan::call('telescope:prune');
        return redirect()->route('backend.systems.telescope.index')->with('message', 'Telescope 数据修剪成功');
    }
}
