<?php

namespace System\Http\Controllers\Backend\Systems;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Log;

class BasesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.base')]),
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
                    'name' => trans('backend.commons.option_setting', ['option' => trans('backend.commons.basic')]),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.bases.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.bases.index'),
                ],
            ],
        ];

        $items = config('app');

        return view('backend::systems.bases.index', compact('items', 'pages'));
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
        $request->validate([
            'name' => 'required|regex:/^[A-Za-z]+$/',
        ], [
            'name.regex' => trans('backend.messages.app_name_tips'),
        ], [
            'name' => trans('backend.commons.app_name'),
        ]);

        if ($request->input('auto_get_url', true)) {
            $request->offsetSet('url', get_server_url());
            $items = $request->except(['_token']);
        } else {
            $items = $request->except('_token');
        }

        settings(['app' => $items]);

        return redirect()->route('backend.systems.bases.index')->with('message', trans('backend.messages.update_success'));
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
