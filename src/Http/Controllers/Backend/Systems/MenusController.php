<?php

namespace System\Http\Controllers\Backend\Systems;

use System\Http\Requests\MenuRequest;
use System\Models\Menu;
use System\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use System\Http\Controllers\Backend\Controller;

class MenusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pages = [
            'title' => trans('backend.commons.menu_manage'),
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
                    'name' => trans('backend.commons.menu_manage'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.menus.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.systems.menus.create'),
                ], [
                    'name' => '刷新',
                    'icon' => 'fa fa-refresh',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.systems.menus.index'),
                ],
            ],
        ];
        $menus = Menu::query()->with('children')->where('parent_id', '0')->get();
        return view('backend::systems.menus.index', compact('menus', 'pages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Menu $menu)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.menu')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.menu_manage'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.menus.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.menu')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.systems.menus.create'),
                    'active' => true,
                ],
            ],
        ];
        $permissions = (new Permission())->getGroupList();
        $menus = Menu::query()->with('children')->where('parent_id', '0')->get();
        $parents = json_encode([0]);
        return view('backend::systems.menus.create_and_edit', compact('pages', 'menu', 'menus', 'parents', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param MenuRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(MenuRequest $request)
    {
        $menu = Menu::create($request->all());
        return redirect()->route('backend.systems.menus.show', $menu->id)->with('message', trans('backend.messages.created_success'));
    }

    /**
     * Display the specified resource.
     *
     * @param Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        $menu or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.menu')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.menu_manage'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.menus.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.menu')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.systems.menus.show', $menu->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.menu')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.systems.menus.edit', $menu->id),
                ],
            ],
        ];
        return view('backend::systems.menus.show', compact('menu', 'pages'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        $menu or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.menu')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.menu_manage'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.systems.menus.index'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.menu')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.systems.menus.show', $menu->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.menu')]),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.systems.menus.show', $menu->id),
                ],
            ],
        ];
        $permissions = (new Permission())->getGroupList();
        $menus = Menu::query()->with('children')->where('parent_id', '0')->get();
        $parents = $menu->getParents()->pluck('id');
        return view('backend::systems.menus.create_and_edit', compact('menu', 'menus', 'parents', 'pages', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param MenuRequest $request
     * @param Menu $menu
     * @return \Illuminate\Http\Response
     */
    public function update(MenuRequest $request, Menu $menu)
    {
        $menu->update($request->all());
        return redirect()->route('backend.systems.menus.index')->with('message', trans('backend.messages.updated_success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Menu $menu
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Menu $menu)
    {
        $menu->delete() or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.systems.menus.index')->with('message', trans('backend.messages.deleted_success'));
    }

    public function ajax(Request $request)
    {
        // 恢复菜单
        if ($request->input('reset')) {
            Artisan::call('sync:menu', ["--force" => true]);
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        // 同步菜单
        if ($request->input('sync')) {
            Artisan::call('sync:menu');
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        try {
            $menus = $request->input('menus');
            $this->updateMenu(json_decode($menus, true));
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        } catch (\Exception $e) {
            return response()->json(api_result(1, $e->getMessage()), 404)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
    }

    public function updateMenu($menus, $parent_id = 0)
    {
        foreach ($menus as $menu) {
            $flight = Menu::updateOrCreate(
                [
                    'id' => $menu['id'],
                    'name' => $menu['name'],
                    'uri' => $menu['uri'],
                    'permission' => $menu['permission'],
                    'icon' => $menu['icon'],
                    //'parent_id' => $menu['parent_id'],
                    'sort' => $menu['sort'],
                    'status' => $menu['status'],
                ],
                ['parent_id' => $parent_id]
            );
            if (!empty($menu['children']) && count($menu['children'])) {
                $this->updateMenu($menu['children'], $flight->id);
            }
        }
    }
}
