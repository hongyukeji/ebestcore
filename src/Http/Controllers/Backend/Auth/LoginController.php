<?php

namespace System\Http\Controllers\Backend\Auth;

use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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
    protected $redirectTo = '/admin';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->middleware('guest.user:admin,backend')->except('logout');
    }

    /**
     * 显示后台登录模板
     */
    public function showLoginForm()
    {
        return view('backend::auth.login');
    }

    /**
     * 使用 admin guard
     */
    protected function guard()
    {
        return Auth::guard('admin');
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
     * 登陆后跳转地址
     */
    public function redirectPath()
    {
        return request('return_url') ?: route('backend.index');
    }

    /**
     * 用户已通过身份验证
     * @param Request $request
     * @param $user
     */
    protected function authenticated(Request $request, $user)
    {
        event(new \System\Events\Admins\AdminLogin($user));
    }

    public function logout(Request $request)
    {
        if ($this->guard()->user()) {
            $this->guard()->logout();
        }
        //$this->guard()->logout();
        //$request->session()->forget($this->guard()->getName());
        //$request->session()->regenerate();
        return redirect(route('backend.auth.login'));
    }
}
