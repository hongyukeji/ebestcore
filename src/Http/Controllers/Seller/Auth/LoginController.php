<?php

namespace System\Http\Controllers\Seller\Auth;

use System\Http\Controllers\Seller\Controller;
use Hongyukeji\Hook\Facades\Hook;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $this->middleware('guest.user:web,seller')->except('logout');
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
        $return_url = request('return_url');
        $url_array = parse_url($return_url);
        $prefix = str_start(config('systems.routes.seller.prefix', 'seller'), '/');
        return starts_with($url_array['path'], $prefix) ? request('return_url') : route('seller.index');
    }

    protected function guard()
    {
        return Auth::guard();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();
        $request->session()->forget($this->guard()->getName());
        $request->session()->regenerate();
        return redirect(route('seller.auth.login'));
    }

    public function showLoginForm()
    {
        return view('seller::auth.login');
    }

    /**
     * 用户已通过身份验证
     * @param Request $request
     * @param $user
     */
    protected function authenticated(Request $request, $user)
    {
        //
    }
}
