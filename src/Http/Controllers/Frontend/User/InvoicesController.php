<?php

namespace System\Http\Controllers\Frontend\User;

use System\Http\Controllers\Frontend\Controller;
use System\Models\UserInvoice;
use System\Http\Requests\UserInvoiceRequest;

class InvoicesController extends Controller
{
    public function index()
    {
        $invoices = auth()->user()->invoices;
        return view('frontend::users.invoices.index', compact('invoices'));
    }

    public function create()
    {
        //
    }

    public function store(UserInvoiceRequest $request)
    {
        UserInvoice::create(array_merge($request->all(), ['user_id' => auth()->user()->id]));
        return redirect()->back()->with('success', '发票添加成功');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(UserInvoiceRequest $request, UserInvoice $invoice)
    {
        $invoice->update($request->all());
        return redirect()->back()->with('success', '发票更新成功');
    }

    public function destroy($id)
    {
        UserInvoice::destroy(explode(',', $id));
        return redirect()->back()->with('success', '发票删除成功');
    }
}
