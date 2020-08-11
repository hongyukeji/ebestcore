<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Controllers\Backend\Controller;

class FontIconController extends Controller
{
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.font_icon'),
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
                    'name' => trans('backend.commons.font_icon'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.font-icon.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.font-icon.index'),
                ],
            ],
        ];
        return view('backend::systems.font-icon.index', compact('pages'));
    }
}
