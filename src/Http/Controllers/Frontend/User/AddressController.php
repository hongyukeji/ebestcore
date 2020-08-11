<?php

namespace System\Http\Controllers\Frontend\User;

use System\Http\Controllers\Frontend\Controller;
use System\Models\UserAddress;
use System\Http\Requests\UserAddressRequest;

class AddressController extends Controller
{
    public function index()
    {
        $address = UserAddress::query()->where('user_id', auth()->id())->orderByDesc('is_default')->get();
        return view('frontend::users.address.index', compact('address'));
    }

    public function create()
    {
        //
    }

    public function store(UserAddressRequest $request)
    {
        UserAddress::create(array_merge($request->all(), ['user_id' => auth()->user()->id]));
        return redirect()->back()->with('success', '收货地址添加成功');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(UserAddressRequest $request, UserAddress $address)
    {
        $address->update($request->all());
        return redirect()->back()->with('success', '收货地址更新成功');
    }

    public function destroy($id)
    {
        UserAddress::destroy(explode(',', $id));
        return redirect()->back()->with('success', '收货地址删除成功');
    }
}
