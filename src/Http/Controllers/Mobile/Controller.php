<?php

namespace System\Http\Controllers\Mobile;

use System\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        if (config('params.intercept.global_user', false)) {
            $this->middleware('auth.user:web,frontend');
        }
    }
}
