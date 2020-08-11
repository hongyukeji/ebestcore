<?php

namespace System\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use System\Http\Requests\SellerShopRequest;
use System\Models\Shop;

class ApplyController extends Controller
{
    public function __construct()
    {
        $this->isShop();
    }

    public function index()
    {
        return view('seller::apply.index');
    }

    public function store(SellerShopRequest $request)
    {
        $user_id = auth()->id();

        $request->offsetSet('audit_status', Shop::AUDIT_STATUS_WAIT);
        $shop = Shop::updateOrCreate(['user_id' => $user_id,], $request->except('_token'));

        return redirect()->route('seller.apply.index', compact('shop'))->with('message', '店铺信息提交成功');
    }

    public function shop()
    {
        $shop = $this->getShop();
        if (!$shop) {
            $user_id = auth()->id();
            $shop = Shop::firstOrNew(['user_id' => $user_id,]);
        }
        return view('seller::apply.shop', compact('shop'));
    }

    /*
     * 申请进度
     */
    public function status()
    {
        $shop = $this->getShop();
        return view('seller::apply.status', compact('shop'));
    }

    public function getShop()
    {
        $user_id = auth()->id();
        return Shop::where('user_id', $user_id)->first();
    }

    /*
     * 检查店铺是否存在
     */
    public function isShop()
    {
        if (Auth::guard()->check()) {
            // 判断当前用户是否存在店铺
            $user_id = Auth::id();
            $shop = Shop::query()->where('user_id', $user_id)->first();
            if ($shop && $shop->audit_status == Shop::AUDIT_STATUS_PASS) {
                return redirect()->route('seller.index')->send();
            }
        }
    }
}
