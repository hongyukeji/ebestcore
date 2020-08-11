<?php

namespace System\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use System\Services\ArrayService;
use System\Services\PluginService;

class PluginsController extends Controller
{
    protected $plugin_service;

    public function __construct()
    {
        parent::__construct();

        $this->plugin_service = new PluginService();
    }

    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.plugins'),
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.plugin'),
                    'icon' => 'fa fa-plug',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.plugins'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.plugins.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.plugins.index'),
                ],
            ],
        ];

        $plugin_service = new PluginService();
        $plugins = $plugin_service->getPlugins();
        $array_service = new ArrayService($plugins);
        $items = $array_service->paginate(15);

        return view('backend::plugins.index', compact('items', 'pages'));
    }

    public function show(Request $request)
    {
        $plugin_name = $request->input('plugin_name');
        $plugin = $this->plugin_service->getPlugin($plugin_name);
        $pages = [
            'title' => trans('backend.commons.plugins'),
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.plugin'),
                    'icon' => 'fa fa-plug',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.plugins'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.plugins.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.plugin')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.plugins.show', ['plugin_name' => $plugin_name]),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.plugins.show', ['plugin_name' => $plugin_name]),
                ],
            ],
        ];
        return view('backend::plugins.show', compact('plugin', 'pages'));
    }

    public function update(Request $request)
    {
        $plugin_name = $request->input('plugin_name');
        $result = false;
        try {
            $plugin = $this->plugin_service->getPlugin($plugin_name);
            if ($request->filled('plugin_status')) {
                $plugin_status = $request->input('plugin_status');
                if (boolval($plugin_status)) {
                    $this->plugin_service->enable($plugin_name);
                } else {
                    $this->plugin_service->disable($plugin_name);
                }
                $result = true;
            }
            if ($request->filled('plugin_install')) {
                $plugin_install = $request->input('plugin_install');
                if (boolval($plugin_install)) {
                    $this->plugin_service->install($plugin_name);
                } else {
                    $this->plugin_service->uninstall($plugin_name);
                }
                $result = true;
            }
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }

        if (request()->ajax()) {
            return $result
                ? response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)
                : response()->json(api_result(1), 501)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }

        return $result
            ? redirect()->route('backend.plugins.index')->with('message', trans('backend.messages.update_success'))
            : redirect()->route('backend.plugins.index')->with('error', trans('backend.messages.update_fail'));
    }

    public function destroy(Request $request)
    {
        $plugin_name = $request->input('plugin_name');
        $result = $this->plugin_service->delete($plugin_name);

        if (request()->ajax()) {
            return $result
                ? response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK)
                : response()->json(api_result(1), 501)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }

        return $result
            ? redirect()->route('backend.plugins.index')->with('message', trans('backend.messages.deleted_success'))
            : redirect()->route('backend.plugins.index')->with('error', trans('backend.messages.deleted_fail'));
    }
}
