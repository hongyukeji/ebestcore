<?php

namespace System\Http\Controllers\Frontend;

class CategoryController extends Controller
{
    public function index()
    {
        //
    }

    public function show($id)
    {
        return redirect()->route('frontend.products.index', ['category_id' => $id,]);
    }
}
