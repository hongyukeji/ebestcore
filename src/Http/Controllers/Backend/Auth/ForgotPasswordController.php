<?php

namespace System\Http\Controllers\Backend\Auth;

use System\Models\Admin;
use System\Services\SmsService;
use System\Services\VerifyCodeService;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Validator;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    public $verify_code_key_prefix = 'backend_verify_code_';

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

    public function showLinkRequestForm()
    {
        return view('backend::auth.forget');
    }

    public function sendVerifyCode(Request $request, VerifyCodeService $verifyCodeService)
    {
        $validator = Validator::make($request->all(), [
            'account' => 'required',
            'captcha' => 'required|captcha',
        ], [
            //'captcha.required' => '验证码不能为空',
            //'captcha.captcha' => '请输入正确的验证码',
        ]);

        $account = $request->input('account');

        $validator->after(function ($validator) use ($account, $verifyCodeService) {
            $user = $verifyCodeService->queryAdminUsername($account);
            if (!$user) {
                $validator->errors()->add('account', is_mobile_number($account) ? '手机号码不存在' : (is_email($account) ? '邮箱不存在' : '请填写正确的手机号码或邮箱'));
            }
        });

        // 验证失败跳转
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $verify_code = config('sms.config.debug', false) ? config('sms.config.default_verify_code', '1234') : mt_rand(1000, 9999);    // 验证码
        $expires_in = config('params.cache.backend.verify_code.expires_at', 60 * 15);  // 过期时间, 单位秒

        // 判断用户名类型
        if (is_mobile_number($account)) {
            // 发送短信验证码
            $result = $verifyCodeService->sendSmsVerifyCode($account, [
                'verify_code' => $verify_code,
            ]);
            if (isset($result['status_code']) && $result['status_code'] == 0) {
                // 将验证码保存至缓存中
                $verifyCodeService->saveVerifyCode($this->verify_code_key_prefix, $account, $verify_code, $expires_in);
                // 跳转至输入验证码界面
                return redirect()->route('backend.auth.forget.verify', ['account' => $account])->with('message', '短信验证码发送成功');
            } else {
                if (app()->isLocal()) {
                    loggers('backend_sms_send_verify_code', $result);
                }
                return redirect()->back()->with('error', '短信验证码发送失败');
            }
        } else {
            // 发送邮件验证码
            $result = $verifyCodeService->sendEmailVerifyCode($account, [
                'verify_code' => $verify_code,
            ]);
            if (isset($result['status_code']) && $result['status_code'] == 0) {
                // 将验证码保存至缓存中
                $verifyCodeService->saveVerifyCode($this->verify_code_key_prefix, $account, $verify_code, $expires_in);
                // 跳转至输入验证码界面
                return redirect()->route('backend.auth.forget.verify', ['account' => $account])->with('message', '邮箱验证码发送成功');
            } else {
                if (app()->isLocal()) {
                    loggers('backend_email_send_verify_code', $result);
                }
                return redirect()->back()->with('error', '邮箱验证码发送失败');
            }
        }
    }

    public function showVerifyForm(Request $request)
    {
        $account = $request->input('account');

        return view('backend::auth.verify', compact('account'));
    }

    public function verify(Request $request, VerifyCodeService $verifyCodeService)
    {
        $validator = $request->validate([
            'account' => 'required',
            'captcha' => 'required',
        ]);

        $account = $request->input('account');
        $captcha = $request->input('captcha');
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

        // 验证通过跳转至重置密码界面
        return redirect()->route('backend.auth.password.reset', [
            'account' => $account,
            'captcha' => $captcha
        ])->with('success', '验证成功, 请设置您的新密码');

    }
}
