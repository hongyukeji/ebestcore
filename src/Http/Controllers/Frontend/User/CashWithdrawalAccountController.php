<?php

namespace System\Http\Controllers\Frontend\User;

use Illuminate\Http\Request;
use System\Http\Controllers\Frontend\Controller;
use System\Models\CashWithdrawalAccount;

class CashWithdrawalAccountController extends Controller
{
    public function index()
    {
        $cash_withdrawal_account = CashWithdrawalAccount::firstOrCreate(
            ['user_id' => auth('web')->id()],
            [
                'status' => true
            ]
        );
        return view('frontend::users.cash-withdrawal-accounts.index', compact('cash_withdrawal_account'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255|min:1',
        ], [], [
            'full_name' => '真实姓名',
        ]);

        $cash_withdrawal_account = CashWithdrawalAccount::firstOrCreate(
            ['user_id' => auth('web')->id()],
            [
                'status' => true
            ]
        );
        $request->offsetSet('user_id', auth()->id());
        $cash_withdrawal_account->update($request->all());
        return redirect()->back()->with('success', '提现账户更新成功！');
    }
}
