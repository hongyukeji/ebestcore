<?php

namespace System\Http\Controllers\Frontend\Auth;

use System\Models\User;
use System\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use System\Rules\MobileVerifyCodeRule;
use System\Services\VerifyCodeService;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest.user:web,frontend');
        $this->middleware('register.intercept');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $user_register_request = new \System\Http\Requests\UserRegisterRequest();
        $rules = $user_register_request->rules();
        $messages = $user_register_request->messages();
        $attributes = $user_register_request->attributes();
        return Validator::make($data, $rules, $messages, $attributes);
    }

    protected function validatorBack(array $data)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'sometimes|nullable|string|email|max:255|unique:users',
            'mobile' => 'required|mobile|unique:users',
            'password' => 'required|string|min:6',
            'password_confirmation' => 'sometimes|string|same:password',
            'captcha' => 'sometimes|captcha',
            'agreement' => 'required|accepted',
            /*'mobile_verify_code' => [
                'required', 'string',
                new MobileVerifyCodeRule(request()->input('mobile')),
            ],*/
        ];
        $attributes = [
            'name' => '用户名',
            'email' => '邮箱',
            'mobile' => '手机号',
            'password' => '密码',
            'captcha' => '验证码',
            'agreement' => '注册协议',
            'mobile_verify_code' => '短信验证码',
        ];
        if (config('params.register.mobile_verify_code', true)) {
            $rules['mobile_verify_code'] = [
                'required', 'string',
                new MobileVerifyCodeRule(request()->input('mobile')),
            ];
        }
        if (config('params.register.image_verify_code', true)) {
            $rules['captcha'] = 'sometimes|captcha';
        }
        return Validator::make($data, $rules, [], $attributes);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \System\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'] ?? null,
            'email' => $data['email'] ?? null,
            'mobile' => $data['mobile'] ?? null,
            'password' => bcrypt($data['password']),
            'status' => true,
        ]);
    }

    public function redirectTo()
    {
        return request('return_url') ?: '/';
    }

    public function showRegistrationForm()
    {
        if (!config('params.auth.user_register')) {
            return redirect()->back()->with('warning', trans('backend.common.admin_register_tips'));
        }
        return view('frontend::auth.register');
    }

    /**
     * 用户已注册。
     *
     * @param \Illuminate\Http\Request $request
     * @param mixed $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        event(new \System\Events\Users\UserRegister($user));
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
