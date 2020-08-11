<?php

namespace System\Http\Controllers\Frontend\User;

use System\Http\Controllers\Frontend\Controller;

class FavoriteController extends Controller
{
    public function index()
    {
        return view('frontend::users.favorites.index');
    }
}
