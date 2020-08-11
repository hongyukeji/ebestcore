<?php

namespace System\Http\Controllers\Frontend\User\Browse;

use System\Http\Controllers\Frontend\Controller;
use System\Models\UserBrowseProduct;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $user_Browse_product = UserBrowseProduct::create([
            'user_id' => auth()->id(),
            'product_id' => $request->input('product_id'),
        ]);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->back()->with('message', trans('backend.messages.created_success'));
    }

    public function destroy($id)
    {
        $product_id = request()->input('product_id');
        if (request()->ajax() || $product_id) {
            UserBrowseProduct::query()->where([
                'user_id' => auth()->id(),
                'product_id' => $product_id,
            ])->delete();
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        UserBrowseProduct::find($id)->delete() or abort(404);
        return redirect()->back()->with('message', trans('backend.messages.deleted_success'));
    }
}
