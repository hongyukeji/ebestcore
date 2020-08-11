<?php

namespace System\Http\Controllers\Backend;

use System\Events\Modules\ModuleDisabledEvent;
use System\Events\Modules\ModuleEnabledEvent;
use System\Events\Modules\ModuleUninstalledEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Nwidart\Modules\Facades\Module;

class ModulesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.modules'),
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.module'),
                    'icon' => 'fa fa-cubes',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.modules'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.modules.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.more'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-outline btn-primary',
                    'link' => route('backend.modules.index'),
                ], [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.modules.index'),
                ],
            ],
        ];

        $modules = Module::toCollection();

        return view('backend::modules.index', compact('modules', 'pages'));
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
        $module_name = $request->input('module_name');

        $module = Module::find($module_name) or abort(404);

        if ($request->has('module_status')) {
            $module_status = $request->input('module_status');
            if ($module_status) {
                //$module->json()->set('active', 1)->save());
                $module->enable();
                event(new ModuleEnabledEvent($module));
            } else {
                $module->disable();
                event(new ModuleDisabledEvent($module));
            }
        }

        if ($request->has('module_delete')) {
            $module->delete();
            event(new ModuleUninstalledEvent($module));
        }

        return redirect()->route('backend.modules.index')->with('message', trans('backend.messages.updated_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param \System\Models\Module $module
     * @return \Illuminate\Http\Response
     */
    public function show(Module $module)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \System\Models\Module $module
     * @return \Illuminate\Http\Response
     */
    public function edit(Module $module)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \System\Models\Module $module
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Module $module)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \System\Models\Module $module
     * @return \Illuminate\Http\Response
     */
    public function destroy(Module $module)
    {
        //
    }
}
