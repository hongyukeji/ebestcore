<?php

namespace System\Http\Controllers\Frontend\User\Browse;

use System\Http\Controllers\Frontend\Controller;
use System\Models\UserBrowseShop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function store(Request $request)
    {
        $user_Browse_shop = UserBrowseShop::create([
            'user_id' => auth()->id(),
            'shop_id' => $request->input('shop_id'),
        ]);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->back()->with('message', trans('backend.messages.created_success'));
    }

    public function destroy($id)
    {
        $shop_id = request()->input('shop_id');
        if (request()->ajax() || !is_null($shop_id)) {
            UserBrowseShop::query()->where([
                'user_id' => auth()->id(),
                'shop_id' => $shop_id,
            ])->delete();
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        UserBrowseShop::find($id)->delete() or abort(404);
        return redirect()->back()->with('message', trans('backend.messages.deleted_success'));
    }
}
