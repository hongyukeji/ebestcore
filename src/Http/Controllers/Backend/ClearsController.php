<?php

namespace System\Http\Controllers\Backend;

use Illuminate\Support\Facades\Artisan;

class ClearsController extends Controller
{
    public function cache()
    {
        //settings(['refresh' => true]);
        Artisan::call("cache:clear");
        return redirect()->back()->with('message', trans('backend.commons.cache') . trans('backend.messages.clear_success'));
    }
}
