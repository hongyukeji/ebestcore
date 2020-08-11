<?php

namespace System\Http\Controllers\Mobile\User;

use System\Http\Controllers\Controller;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $account = auth()->user()->account;
        return view('mobile::users.accounts.index', compact('account', 'user'));
    }
}
