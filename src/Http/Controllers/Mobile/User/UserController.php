<?php

namespace System\Http\Controllers\Mobile\User;

use Illuminate\Http\Request;
use System\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use System\Models\Navigation;
use System\Models\User;

class UserController extends Controller
{

    public function index()
    {
        $user = Auth::user();
        $more_navigations = Navigation::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.navigations.user_more_navigations'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        return view('mobile::users.index', compact('user', 'more_navigations'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $user = User::query()->find($id) or abort(404);
        return view('mobile::users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
