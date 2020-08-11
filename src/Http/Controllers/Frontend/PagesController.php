<?php

namespace System\Http\Controllers\Frontend;

use System\Models\Page;

class PagesController extends Controller
{
    public function show($id)
    {
        if (is_numeric($id)) {
            $page = Page::query()->find($id)->first();
        } else {
            $page = Page::query()->where('slug', $id)->first();
        }
        $page or abort(404);
        return view('frontend::pages.show', compact('page'));
    }
}
