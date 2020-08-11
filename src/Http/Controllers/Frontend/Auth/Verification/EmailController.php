<?php

namespace System\Http\Controllers\Frontend\Auth\Verification;

use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\Request;
use System\Http\Controllers\Controller;
use System\Models\User;

class EmailController extends Controller
{
    use VerifiesEmails;

    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:60,10')->only('verify', 'resend');
    }

    public function show(Request $request)
    {
        return $request->user()->hasVerifiedEmail()
            ? redirect($this->redirectPath())
            : view('frontend::auth.verify.email');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'sometimes|email|unique:users,email,' . $request->user()->id,
        ], [], [
            'email' => '邮箱',
        ]);

        if ($request->filled('email')) {
            $request->user()->update([
                'email' => $request->input('email'),
            ]);
        }

        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectPath());
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('resent', true);
    }

    public function redirectTo()
    {
        return route_url('frontend.user.index');
    }
}
