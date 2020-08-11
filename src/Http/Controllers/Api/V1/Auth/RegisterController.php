<?php

namespace System\Http\Controllers\Api\V1\Auth;

use System\Http\Controllers\Api\Controller;
use System\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use System\Http\Requests\UserRequest;
use System\Http\Resources\UserResource;
use System\Services\VerifyCodeService;
use Tymon\JWTAuth\Facades\JWTAuth;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('register.intercept');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|min:4|unique:users,name',
            'password' => 'required|string|max:255|min:6',
        ]);

        $user = User::create([
            'name' => $request->input('name'),
            'password' => bcrypt($request->input('password')),
            'status' => true,
        ]);

        return api_result(0, null, $this->respondWithToken($user));
    }

    protected function respondWithToken($user)
    {
        $token = JWTAuth::fromUser($user);
        return [
            'user_info' => User::find($user->id),
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ];
    }

    public function mobile()
    {
        //
    }

    public function sendVerifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|mobile|unique:users,mobile',
        ]);

        if ($validator->fails()) {
            return api_result(1, null, $validator->errors());
        }

        $mobile = $request->input('mobile');

        $verifyCodeService = new VerifyCodeService();
        $verify_code_key_prefix = config('params.cache.backend.verify_code.prefix', 'backend_verify_code_');
        $verify_code = config('sms.config.debug', false) ? config('sms.config.default_verify_code', '1234') : mt_rand(1000, 9999);    // 验证码
        $expires_in = config('params.cache.backend.verify_code.expires_at', 60 * 15);  // 过期时间, 单位秒

        // 发送短信验证码
        $result = $verifyCodeService->sendSmsVerifyCode($mobile, ['verify_code' => $verify_code,]);
        if (isset($result['status_code']) && $result['status_code'] == 0) {
            // 将验证码保存至缓存中
            $verifyCodeService->saveVerifyCode($verify_code_key_prefix, $mobile, $verify_code, $expires_in);
            return api_result(0, null);
        } else {
            if (app()->isLocal()) {
                loggers('backend_sms_send_verify_code', $result);
            }
            return api_result(1, isset($result['message']) ? $result['message'] : null);
        }
    }
}
