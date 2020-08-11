<?php

namespace System\Http\Controllers\Mobile;

class CategoryController extends Controller
{
    public function index()
    {
        return view('mobile::categories.index');
    }

    public function show($id)
    {
        return redirect()->route('mobile.products.index', ['category_id' => $id,]);
    }
}
