<?php

namespace System\Http\Controllers\Backend;

use System\Models\ProductExtend;
use System\Handlers\FileUploadsHandler;
use System\Http\Controllers\Backend\Controller;
use System\Models\Category;
use System\Models\Product;
use System\Http\Requests\ProductRequest;
use System\Repository\Interfaces\CategoryInterface;
use System\Repository\Interfaces\ProductInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductsController extends Controller
{
    protected $product;

    protected $category;

    public function __construct(ProductInterface $product, CategoryInterface $category)
    {
        $this->product = $product;
        $this->category = $category;
    }

    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.products'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.products.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.products.create'),
                ], [
                    'name' => trans('backend.commons.delete'),
                    'icon' => 'fa fa-trash-o',
                    'class' => 'btn btn-danger ajax-delete',
                    'link' => 'javascript:;',
                ], [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'id' => '',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.products.index'),
                ],
            ],
        ];
        $items = $this->product->search();
        return view('backend::products.index', compact('items', 'pages'));
    }

    public function show(Product $product)
    {
        $product or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.product')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.products.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.products.show', $product->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.products.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.products.edit', $product->id),
                ],
            ],
        ];
        return view('backend::products.show', compact('product', 'pages'));
    }

    public function create(Product $product)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.product')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.products.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.products.create'),
                    'active' => true,
                ],
            ],
        ];
        //$categories = $this->category->findTrees();
        //request()->session()->put('form_token_product', uniqid(mt_rand(), true));
        return view('backend::products.create_and_edit', compact('pages', 'product'));
    }

    public function store(ProductRequest $request)
    {
        //$request->offsetSet('shop_id', 0);
        // 创建商品
        $product = Product::create($request->all());
        return redirect()->route('backend.products.show', $product->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Product $product)
    {
        $product or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.products.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.products.edit', $product->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.products.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.products.show', $product->id),
                ],
            ],
        ];
        return view('backend::products.create_and_edit', compact('product', 'pages'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product or abort(404);
        $product->update($request->all());
        return redirect()->route('backend.products.show', $product->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Product::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.products.index')->with('message', trans('backend.messages.deleted_success'));
    }

    public function batchUpdate(Request $request)
    {
        $id_array = explode(',', $request->input('id'));
        $column_key = $request->input('column_key');
        $column_val = $request->input('column_val');
        Product::whereIn('id', $id_array)->update([$column_key => $column_val]);
        return redirect()->back()->with('message', "批量更新成功！");
    }
}
