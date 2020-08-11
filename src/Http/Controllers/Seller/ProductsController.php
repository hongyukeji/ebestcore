<?php

namespace System\Http\Controllers\Seller;

use System\Models\Product;
use System\Http\Requests\ProductRequest;
use System\Repository\Interfaces\CategoryInterface;
use System\Repository\Interfaces\ProductInterface;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $product;

    protected $category;

    protected $shop_id;

    public function __construct(ProductInterface $product, CategoryInterface $category)
    {
        if (isset(auth('web')->user()->shop)) {
            $this->shop_id = auth('web')->user()->shop->id;
        }
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
                    'link' => route('seller.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('seller.products.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('seller.products.create'),
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
                    'link' => route('seller.products.index'),
                ],
            ],
        ];
        $request->offsetSet('shop_id', $this->shop_id);
        $items = $this->product->search($this->shop_id);
        return view('seller::products.index', compact('items', 'pages'));
    }

    public function show(Product $product)
    {
        $product->shop_id == $this->shop_id or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.product')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('seller.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('seller.products.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('seller.products.show', $product->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('seller.products.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('seller.products.edit', $product->id),
                ],
            ],
        ];
        return view('seller::products.show', compact('product', 'pages'));
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
                    'link' => route('seller.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('seller.products.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('seller.products.create'),
                    'active' => true,
                ],
            ],
        ];
        //$categories = $this->category->findTrees();
        //request()->session()->put('form_token_product', uniqid(mt_rand(), true));
        return view('seller::products.create_and_edit', compact('pages', 'product'));
    }

    public function store(ProductRequest $request)
    {
        $this->requestFilter();
        // 创建商品
        $request->offsetSet('shop_id', $this->shop_id);
        $product = Product::create($request->all());
        return redirect()->route('seller.products.show', $product->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Product $product)
    {
        $product->shop_id == $this->shop_id or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('seller.index'),
                ], [
                    'name' => trans('backend.commons.products'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('seller.products.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('seller.products.edit', $product->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('seller.products.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('seller.products.show', $product->id),
                ],
            ],
        ];
        return view('seller::products.create_and_edit', compact('product', 'pages'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $this->requestFilter();
        $product->shop_id == $this->shop_id or abort(404);
        $product->update($request->all());
        return redirect()->route('seller.products.show', $product->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);
        $products = Product::query()->whereIn('id', $ids)->where('shop_id', $this->shop_id)->get();
        if ($products) {
            Product::destroy($products->pluck('id')) or abort(404);
        }
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('seller.products.index')->with('message', trans('backend.messages.deleted_success'));
    }

    public function requestFilter()
    {
        $request = request();
        $request->offsetSet('shop_id', $this->shop_id);
        $request->offsetUnset('sale_count');
        $request->offsetUnset('browse_count');
        $request->offsetUnset('comment_count');
        $request->offsetUnset('favorite_count');
        $request->offsetUnset('good_count');
        $request->offsetUnset('mid_count');
        $request->offsetUnset('bad_count');
        $request->offsetUnset('score');
        $request->offsetUnset('audit_status');
        $request->offsetUnset('audit_remark');
    }
}
