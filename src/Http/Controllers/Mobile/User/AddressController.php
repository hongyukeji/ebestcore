<?php

namespace System\Http\Controllers\Mobile\User;

use System\Http\Controllers\Controller;
use System\Http\Requests\UserAddressRequest;
use System\Models\UserAddress;

class AddressController extends Controller
{
    public function index()
    {
        $address = UserAddress::query()->where('user_id', auth()->id())->orderByDesc('is_default')->get();
        return view('mobile::users.address.index', compact('address'));
    }

    public function create(UserAddress $userAddress)
    {
        return view('mobile::users.address.create_and_edit', compact('userAddress'));
    }

    public function store(UserAddressRequest $request)
    {
        UserAddress::create(array_merge($request->except(['return_url']), ['user_id' => auth()->user()->id]));
        if (!empty($return_url = $request->get('return_url'))) {
            return redirect($return_url)->with('message', '收货地址添加成功');
        }
        return redirect()->route('mobile.user.address.index')->with('message', '收货地址添加成功');
    }

    public function show($id)
    {
        $userAddress = UserAddress::query()->findOrFail($id) or abort(404);
        return view('mobile::users.address.show', compact('userAddress'));
    }

    public function edit($id)
    {
        $userAddress = UserAddress::query()->findOrFail($id) or abort(404);
        return view('mobile::users.address.create_and_edit', compact('userAddress'));
    }

    public function update(UserAddressRequest $request, $id)
    {
        $userAddress = UserAddress::query()->findOrFail($id) or abort(404);
        $userAddress->update(array_merge($request->except(['return_url']), ['user_id' => auth()->user()->id]));
        if (!empty($return_url = $request->get('return_url'))) {
            return redirect($return_url)->with('message', '收货地址更新成功');
        }
        return redirect()->route('mobile.user.address.index')->with('message', '收货地址更新成功');
    }

    public function destroy($id)
    {
        UserAddress::destroy(explode(',', $id));
        return redirect()->back()->with('message', '收货地址删除成功');
    }
}
