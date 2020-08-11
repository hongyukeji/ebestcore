<?php

namespace System\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use System\Http\Controllers\Frontend\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('frontend::users.profiles.index', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user() or abort(404);
        $id = $user->id;
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|nullable|max:255|unique:users,name,' . $id,
            'mobile' => 'sometimes|mobile|nullable|max:255|unique:users,mobile,' . $id,
            'email' => 'sometimes|email|nullable|max:255|unique:users,email,' . $id,
            'avatar' => 'sometimes|file|nullable',
            'password' => 'sometimes|string|nullable|min:6|max:255',
        ])->validate();

        $user->update($request->all());
        return redirect()->back()->with('message', trans('backend.messages.updated_success'));
    }
}
