<?php

namespace System\Http\Controllers\Backend\Sites;

use Hongyukeji\LaravelSettings\Facades\Settings;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Storage;

class BasesController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.basic')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.basic')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.bases.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.bases.index'),
                ],
            ],
        ];

        $items = config('websites.basic');

        return view('backend::sites.bases.index', compact('items', 'pages'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $items = $request->except('_token');

        // logo图片
        if ($request->hasFile('site_logo_file')) {
            // 删除旧文件
            $original_file = config('websites.basic.site_logo');
            if (Storage::exists($original_file)) {
                Storage::delete($original_file);
            }
            // 获取文件相关信息
            $file = $request->file('site_logo_file');
            $ext = $file->getClientOriginalExtension();
            $file_name = uuid() . ".{$ext}";
            $file_path = $file->storeAs(uploads_path('site.image'), $file_name);
            $items['site_logo'] = $file_path;
        }

        // logo 长方形图片
        if ($request->hasFile('site_logo_rectangle_file')) {
            // 删除旧文件
            $original_file = config('websites.basic.site_logo_rectangle');
            if (Storage::exists($original_file)) {
                Storage::delete($original_file);
            }
            // 获取文件相关信息
            $file = $request->file('site_logo_rectangle_file');
            $ext = $file->getClientOriginalExtension();
            $file_name = uuid() . ".{$ext}";
            $file_path = $file->storeAs(uploads_path('site.image'), $file_name);
            $items['site_logo_rectangle'] = $file_path;
        }

        // 网站Favicon图标
        if ($request->hasFile('site_favicon_file')) {
            // 删除旧文件
            $original_file = config('websites.basic.site_favicon');
            if (Storage::exists($original_file)) {
                Storage::delete($original_file);
            }
            // 获取文件相关信息
            $file = $request->file('site_favicon_file');
            $ext = $file->getClientOriginalExtension();
            $file_name = uuid() . ".{$ext}";
            $file_path = $file->storeAs(uploads_path('site.image'), $file_name);
            $items['site_favicon'] = $file_path;
            @move_uploaded_file($request->file('site_favicon_file'), 'favicon.ico');
        }

        unset($items['site_logo_file']);
        unset($items['site_logo_rectangle_file']);
        unset($items['site_favicon_file']);

        settings(['websites' => ['basic' => array_replace_recursive(config('websites.basic'), $items),]]);

        return redirect()->route('backend.sites.bases.index')->with('message', trans('backend.messages.update_success'));
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
