<?php

namespace System\Http\Controllers\Frontend\User;

use System\Http\Controllers\Frontend\Controller;

class BrowseController extends Controller
{
    public function index()
    {
        return view('frontend::users.browses.index');
    }
}
