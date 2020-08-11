<?php

namespace System\Http\Controllers\Backend\Auth;

use System\Models\Admin;
use System\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;
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
            'mobile' => 'required|mobile',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'required|boolean',
        ], [
            'terms.required' => '您必须同意:attribute',
        ], [
            'terms' => '注册协议',
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
        return Admin::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'password' => bcrypt($data['password']),
            'status' => true,
        ]);
    }

    protected function guard()
    {
        return auth()->guard('admin');
    }

    public function showRegistrationForm()
    {
        if (!config('params.auth.admin_register')) {
            return redirect()->back()->with('message', trans('backend.commons.register_tips'));
        }
        return view('backend::auth.register');
    }

    public function register(Request $request)
    {
        if (!config('params.auth.admin_register')) {
            return redirect()->back()->with('warning', trans('backend.commons.admin_register_tips'));
        }
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
            ?: redirect($this->redirectPath());
    }

    public function redirectPath()
    {
        return request('return_url') ?: route('backend.index');
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
        event(new \System\Events\Admins\AdminRegister($user));
    }
}
