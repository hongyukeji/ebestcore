<?php

namespace System\Http\Controllers\Backend\Systems;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class FilesystemsController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.storage')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.global'),
                    'icon' => 'fa fa-cog',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.storage')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.filesystems.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.filesystems.index'),
                ],
            ],
        ];

        $items = config('filesystems');

        return view('backend::systems.filesystems.index', compact('items', 'pages'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        settings(['imageup' => ['upload_disk' => $request->input('default', 'public')]]);
        settings(['elfinder' => ['disks' => $request->input('elfinder.disks')]]);
        $items = $request->except(['_token', 'elfinder']);
        settings(['filesystems' => $items]);
        return redirect()->route('backend.systems.filesystems.index')->with('message', trans('backend.messages.update_success'));
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
