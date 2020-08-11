<?php

namespace System\Http\Controllers\Backend;

use Illuminate\Http\Request;
use System\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{
    public function __construct()
    {
        //parent::__construct();
        //$this->middleware(['auth.admin:admin', 'auth.permission:admin,resource']);
    }
}
