<?php

namespace System\Http\Controllers\Api\V1\User;

use System\Http\Controllers\Api\Controller;
use System\Models\UserAddress;
use Illuminate\Http\Request;
use System\Http\Resources\UserAddressResource;
use System\Services\CartService;

class AddressController extends Controller
{
    public function index()
    {
        $addresses = UserAddress::query()->where('user_id', auth()->id())->get();
        return UserAddressResource::collection($addresses)->additional(api_result(0, null));
    }

    public function store(Request $request)
    {
        $addresses = UserAddress::query()->where('user_id', auth()->id())->get();
        if ($addresses->count() >= 5) {
            return api_result(1, '超出收货地址最大数量，请删除已有收货地址，再添加新的收货地址');
        }
        $request->offsetSet('user_id', auth()->id());
        $address = UserAddress::create($request->all());
        return api_result(0, null, new UserAddressResource($address));
    }

    public function show($id)
    {
        $address = UserAddress::query()->where('id', $id)->where('user_id', auth()->id())->first() or abort(404);
        return api_result(0, null, new UserAddressResource($address));
    }

    public function update(Request $request, $id)
    {
        $address = UserAddress::query()->where('id', $id)->where('user_id', auth()->id())->first() or abort(404);
        $request->offsetSet('user_id', auth()->id());
        $address->update($request->all());
        return api_result(0, null, new UserAddressResource($address));
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);
        UserAddress::query()->whereIn('id', $ids)->where('user_id', auth()->id())->delete();
        return api_result(0);
    }
}
