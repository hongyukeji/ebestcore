<?php

namespace System\Http\Controllers\Seller\Settings;

use Illuminate\Http\Request;
use System\Http\Controllers\Seller\Controller;
use System\Http\Requests\ShopRequest;
use System\Models\Shop;

class BasesController extends Controller
{
    public function index()
    {
        $shop = Shop::firstOrCreate(
            ['user_id' => auth('web')->id()],
            []
        );
        return view('seller::settings.bases.index', compact('shop'));
    }

    public function store(Request $request)
    {
        $shop = Shop::query()->where('user_id', auth('web')->id())->first() or abort(404);

        $request->validate([
            'name' => 'required|string|nullable|max:255|unique:shops,name,' . $shop->id,
            'description' => 'sometimes|string|nullable|max:255',
        ], [], [
            'name' => '店铺名称',
            'description' => '店铺描述',
        ]);

        $shop->update($request->except(['audit_status', 'audit_remark', 'sort']));
        return redirect()->route('seller.settings.bases.index')->with('message', trans('backend.messages.update_success'));
    }
}
