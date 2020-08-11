<?php

namespace System\Http\Controllers\Common;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IndexController extends Controller
{
    public function index()
    {
        return view('common::index.index');
    }
}
