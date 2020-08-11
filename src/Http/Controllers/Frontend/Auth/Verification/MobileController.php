<?php

namespace System\Http\Controllers\Frontend\Auth\Verification;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use System\Http\Controllers\Frontend\Controller;
use System\Services\VerifyCodeService;

class MobileController extends Controller
{
    public function show(Request $request)
    {
        return $request->user()->hasVerifiedMobile()
            ? redirect($this->redirectPath())
            : view('frontend::auth.verify.mobile');
    }

    public function resend(Request $request)
    {
        $user_id = auth('web')->id();
        $request->validate([
            'mobile' => 'sometimes|mobile|unique:users,mobile,' . $user_id,
        ], [], [
            'mobile' => '手机号',
        ]);

        if ($request->filled('mobile')) {
            $request->user()->update([
                'mobile' => $request->input('mobile'),
            ]);
        }

        if ($request->user()->hasVerifiedMobile()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendMobileVerificationNotification();

        $mobile = $request->user()->getMobileForVerification();

        return view('frontend::auth.verify.mobile', compact('mobile'));
    }

    /**
     * Mark the authenticated user's mobile address as verified.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request)
    {
        if (!hash_equals((string)$request->route('id'), (string)$request->user()->getKey())) {
            throw new AuthorizationException;
        }

        if (!hash_equals((string)$request->input('mobile'), $request->user()->getMobileForVerification())) {
            throw new AuthorizationException;
        }

        $verifyCodeService = new VerifyCodeService();
        $verify_code_key_prefix = config('params.cache.backend.verify_code.prefix', 'backend_verify_code_');
        if (!$verifyCodeService->checkVerifyCode($verify_code_key_prefix, $request->input('mobile'), $request->input('verify_code'))) {
            return redirect()->back()->with('error', '短信验证码错误！');
        }

        if ($request->user()->hasVerifiedMobile()) {
            return redirect($this->redirectPath());
        }

        if ($request->user()->markMobileAsVerified()) {
            event(new Verified($request->user()));
        }

        return redirect($this->redirectPath())->with('verified', true);
    }

    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo() : '/home';
    }

    public function redirectTo()
    {
        return route_url('frontend.user.index');
    }
}
