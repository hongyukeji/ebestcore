<?php

namespace System\Http\Controllers\Api\V1\User;

use System\Http\Controllers\Api\Controller;
use System\Models\User;
use System\Http\Resources\UserResource;
use System\Repository\Interfaces\UserInterface;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function index()
    {
        $user_id = app('Dingo\Api\Auth\Auth')->user()->id; // auth()->user()
        $user = User::query()->find($user_id);
        return api_result(0, null, new UserResource($user));
    }
}
