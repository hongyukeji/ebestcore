<?php

namespace System\Http\Controllers\Frontend\User\Favorite;

use System\Http\Controllers\Frontend\Controller;
use System\Models\UserFavorite;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function store(Request $request)
    {
        $user_favorite_shop = UserFavorite::create([
            'user_id' => auth()->id(),
            'favorite_type' => UserFavorite::FAVORITE_TYPE_SHOP,
            'favorite_id' => $request->input('shop_id'),
        ]);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->back()->with('message', trans('backend.messages.created_success'));
    }

    public function destroy($id)
    {
        $request = request();
        $shop_id = request()->input('shop_id');
        if ($request->filled('shop_id')) {
            UserFavorite::query()->where([
                'user_id' => auth()->id(),
                'favorite_type' => UserFavorite::FAVORITE_TYPE_SHOP,
                'favorite_id' => $shop_id,
            ])->delete();
            if (request()->ajax() || !is_null($shop_id)) {
                return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            }
            return redirect()->back()->with('message', '收藏店铺删除成功！');
        }
        UserFavorite::find($id)->delete() or abort(404);
        return redirect()->back()->with('message', trans('backend.messages.deleted_success'));
    }
}
