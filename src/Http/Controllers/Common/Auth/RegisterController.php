<?php

namespace System\Http\Controllers\Common\Auth;

use System\Models\User;
use System\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
        $this->middleware('register.intercept');
        $this->middleware('guest.user:web,common');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255',
            'common' => 'required|common',
            'password' => 'required|string|min:6|confirmed',
        ]);
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
            'name' => $data['name'],
            'email' => $data['email'],
            'common' => $data['common'],
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
        return view('common::auth.register');
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
}
