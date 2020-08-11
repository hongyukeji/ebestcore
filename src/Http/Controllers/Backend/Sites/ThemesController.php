<?php

namespace System\Http\Controllers\Backend\Sites;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class ThemesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.theme')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.theme')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.themes.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => '刷新',
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.sites.themes.index'),
                ],
            ],
        ];

        $templates = config('themes.templates');
        $themes = [];
        $view_path = str_finish(config('themes.view_path', resource_path('views')), DIRECTORY_SEPARATOR);
        foreach ($templates as $key => $template) {
            $themes[$key] = $template;
            $theme_base_directory = "{$view_path}{$template['path_prefix']}";
            $directories = get_template_dir($theme_base_directory, []);
            foreach ($directories as $directory) {
                $package_path = str_finish($theme_base_directory, DIRECTORY_SEPARATOR) . $directory . DIRECTORY_SEPARATOR . "package.json";
                $theme = [];
                if (@file_exists($package_path)) {
                    $package = @file_get_contents($package_path);
                    $theme = @json_decode($package, true) ?? [];
                }
                if ($template['template'] == $directory) {
                    $theme['active'] = true;
                }
                $themes[$key]['templates'][$directory] = $theme;
            }
        }

        return view('backend::sites.themes.index', compact('themes', 'pages'));
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
        $template_terminal = $request->input('template_terminal', '');
        $template_name = $request->input('template_name', '');
        settings(['themes' => ['templates' => array_replace_recursive(config('themes.templates'), [$template_terminal => ['template' => $template_name]])]]);
        return redirect()->route('backend.sites.themes.index')->with('message', trans('backend.messages.update_success'));
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
