<?php

namespace System\Http\Controllers\Frontend\User;

use System\Http\Controllers\Frontend\Controller;
use System\Models\UserCard;

class CardsController extends Controller
{
    public function index()
    {
        $cards = UserCard::query()->where('user_id', auth('web')->id())->get();
    }

    public function update()
    {
        //
    }
}
