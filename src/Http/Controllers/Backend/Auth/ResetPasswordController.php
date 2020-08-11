<?php

namespace System\Http\Controllers\Backend\Auth;

use Illuminate\Support\Facades\Hash;
use System\Services\VerifyCodeService;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Validator;

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

    public $verify_code_key_prefix = 'backend_verify_code_';

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest.user:admin,backend');
        $this->verify_code_key_prefix = config('params.cache.backend.verify_code.prefix', 'backend_verify_code_');
    }

    public function showResetForm(Request $request)
    {
        $this->validate($request, [
            'account' => 'required',
            'captcha' => 'required',
        ]);

        $account = $request->input('account');
        $captcha = $request->input('captcha');

        return view('backend::auth.reset')->with(
            [
                'account' => $account,
                'captcha' => $captcha
            ]
        );
    }

    public function reset(Request $request, VerifyCodeService $verifyCodeService)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required',
            'captcha' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);

        $account = $request->input('account');
        $captcha = $request->input('captcha');
        $password = $request->input('password');

        $user = $verifyCodeService->queryAdminUsername($account);

        // 判断验证码是否正确
        $validator->after(function ($validator) use ($account, $captcha, $verifyCodeService, $user) {
            if (!$user) {
                $validator->errors()->add('captcha', '验证码和用户不匹配');
            }
            if (!$verifyCodeService->checkVerifyCode($this->verify_code_key_prefix, $account, $captcha)) {
                $validator->errors()->add('captcha', '验证码不正确或已经过期');
            }
        });

        // 验证失败跳转
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // 验证成功, 重置密码
        $this->resetPassword($user, $password);

        // 清除验证码缓存
        $verifyCodeService->clearVerifyCode($this->verify_code_key_prefix, $account);

        return redirect($this->redirectPath())->with('status', '密码重置成功');
    }

    protected function resetPassword($user, $password)
    {
        //$user->password = Hash::make($password);
        $user->password = $password;
        $user->save();
        $this->guard()->login($user);
    }

    protected function guard()
    {
        return auth()->guard('admin');
    }

    public function redirectPath()
    {
        return route('backend.index');
        //return route('backend.auth.login');
    }
}
