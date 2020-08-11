<?php

namespace System\Http\Controllers\Api\V1\Auth;

use System\Http\Controllers\Api\Controller;
use System\Models\User;
use Hongyukeji\Hook\Facades\Hook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use System\Http\Resources\UserResource;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    public function login(Request $request)
    {
        if (!$token = $this->attemptLogin($request)) {
            //return response()->json(['error' => 'Unauthorized'], 401);
            return api_result(401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return api_result(0, 'Successfully logged out');
        //return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        //$user = Auth::guard('api')->user();
        $user = auth('api')->user();
        $data = [
            'token' => 'Bearer ' . $token,
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ];
        if (request()->input('get_user_info', false)) {
            $data['user_info'] = new UserResource($user);
        }
        return api_result(0, null, $data);
    }

    protected function attemptLogin(Request $request)
    {
        return collect(config('params.auth.login_field'))->transform(function ($value) use ($request) {
            return Auth::guard('api')->attempt([
                $value => $request->input(config('params.auth.login_name')),
                'password' => $request->input('password')
            ]);
        })->first(function ($value) {
            return $value;
        });
    }
}
