<?php

namespace System\Http\Controllers\Mobile\User;

use System\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('mobile::users.profiles.index', compact('user'));
    }
}
