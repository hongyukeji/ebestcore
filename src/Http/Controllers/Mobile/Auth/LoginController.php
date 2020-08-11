<?php

namespace System\Http\Controllers\Mobile\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use System\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use System\Models\User;
use System\Rules\MobileVerifyCodeRule;
use System\Services\VerifyCodeService;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest.user:web,mobile')->except('logout');
    }

    /**
     * 用户名验证
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return collect(config('params.auth.login_field'))->contains(function ($value) use ($request) {
            $username = $request->get($this->username());
            $password = $request->get('password');
            return $this->guard()->attempt([
                $value => $username, 'password' => $password, 'status' => true
            ], $request->filled('remember'));
        });
    }

    /**
     * 重写验证时使用的用户名字段
     */
    public function username()
    {
        return config('params.auth.login_name');
    }

    /**
     * 重写登录后跳转地址为上一页
     * @return string
     */
    public function redirectTo()
    {
        return request('return_url') ?: route('mobile.index');
    }

    protected function guard()
    {
        return Auth::guard();
    }

    public function logout(Request $request)
    {
        if ($this->guard()->user()) {
            $this->guard()->logout();
        }
        //$this->guard()->logout();
        //$request->session()->forget($this->guard()->getName());
        //$request->session()->regenerate();
        return redirect()->back() ?: redirect()->route('mobile.index');
    }

    public function showLoginForm()
    {
        return view('mobile::auth.login');
    }

    /**
     * 用户已通过身份验证
     * @param Request $request
     * @param $user
     */
    protected function authenticated(Request $request, $user)
    {
        event(new \System\Events\Users\UserLogin($user));
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'mobile' => 'required|mobile',
            'return_url' => 'sometimes|string',
            'password' => 'sometimes|string|min:6',
            'mobile_verify_code' => [
                'required', 'string',
                new MobileVerifyCodeRule($request->input('mobile')),
            ],
        ], [], [
            'mobile' => '手机号',
            'mobile_verify_code' => '短信验证码',
        ]);

        $user = User::updateOrCreate([
            'mobile' => $request->input('mobile')
        ], [
            'mobile_verified_at' => now(),
            'status' => true,
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
            $user->save();
        }

        // 验证成功进行登录跳转
        Auth::guard('web')->login($user, $request->filled('remember'));

        return redirect($this->redirectTo());
    }

    public function showVerifyCodeForm()
    {
        return view('mobile::auth.verify_code');
    }

    public function sendVerifyCode(Request $request)
    {
        $request->validate([
            'mobile' => 'required|mobile',
        ], [], [
            'mobile' => '手机号',
        ]);

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
