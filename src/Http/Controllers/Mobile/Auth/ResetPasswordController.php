<?php

namespace System\Http\Controllers\Mobile\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use System\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use System\Models\User;
use System\Rules\MobileVerifyCodeRule;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest.user:web,mobile');
    }

    public function resetMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required|mobile',
            'return_url' => 'sometimes|string',
            'mobile_verify_code' => [
                'required', 'string',
                new MobileVerifyCodeRule($request->input('mobile')),
            ],
            'password' => 'required|string|min:6',
            'password_confirmation' => 'sometimes|string|same:password',
        ], [], [
            'mobile' => '手机号',
            'mobile_verify_code' => '短信验证码',
            'password' => '密码',
            'password_confirmation' => '确认密码',
        ]);

        // 验证成功修改密码
        $user = User::updateOrCreate([
            'mobile' => $request->input('mobile')
        ], [
            'password' => bcrypt($request->input('password')),
        ]);
        if (empty($user->mobile_verified_at)) {
            $user->mobile_verified_at = now();
            $user->save();
        }

        // 验证成功进行登录跳转
        Auth::guard('web')->login($user, $request->filled('remember'));

        return redirect($this->redirectTo())->with('message', '密码重置成功！');
    }

    /**
     * 重写登录后跳转地址为上一页
     * @return string
     */
    public function redirectTo()
    {
        return request('return_url') ?: route('mobile.index');
    }
}
