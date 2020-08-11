<?php

namespace System\Http\Controllers\Frontend\User;

use Illuminate\Support\Facades\DB;
use System\Http\Controllers\Frontend\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use System\Models\CashWithdrawal;
use System\Services\UserAccountService;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $account = auth()->user()->account;
        //$account_logs = [];
        //$cash_withdrawals = CashWithdrawal::query()->where('user_id',auth()->id())->get();
        return view('frontend::users.accounts.index', compact('account', 'user'));
    }

    /*
     * 余额充值
     */
    public function rechargeBalance(Request $request)
    {
        $request->validate([
            'money' => 'required|integer|max:999999999999',
            'payment_gateway' => 'required|string|max:255',
        ], [], [
            'money' => '金额',
            'payment_gateway' => '支付服务商',
        ]);
        $money = $request->input('money');
        $payment_gateway = $request->input('payment_gateway');
        $payment_log = (new UserAccountService)->createRechargeBalanceOrder($money);
        return redirect(route_url('frontend.payments.handle', ['payment_gateway' => $payment_gateway, 'payment_no' => $payment_log->payment_no]));
    }

    /*
     * 提现申请
     */
    public function cashWithdrawal(Request $request)
    {
        $request->offsetSet('user_id', auth()->id());
        $validator = Validator::make($request->all(), [
            'money' => 'required|integer|max:999999999999|min:1',
            'cash_withdrawal_account_id' => 'required|integer|exists:cash_withdrawal_accounts,id',
        ], [], [
            'money' => '提现金额',
            'cash_withdrawal_account_id' => '提现账户',
        ]);

        // 数据验证前
        $validator->after(function ($validator) use ($request) {
            // 判断账户余额是否充足
            if (auth('web')->user()->account->money < $request->input('money')) {
                $validator->errors()->add('money', '对不起，您的账户余额不足!');
            }
        });

        // 验证失败跳转
        if ($validator->fails()) {
            //$validator->errors()->first(); //返回第一个错误消息，一般用这个就行了
            //$validator->errors()->all(); //返回全部错误消息，不带表单下标
            //$validator->errors(); //返回全部错误消息，带表单下标
            return redirect()->back()->withErrors($validator);
        }

        // 开启数据库事务处理
        DB::transaction(function () use ($request) {
            $user_id = $request->input('user_id');
            $money = $request->input('money');

            // 扣除现有余额 并 增加冻结余额
            DB::table('user_accounts')->where('user_id', $user_id)->decrement('freeze_money', $money);  // 自减
            DB::table('user_accounts')->where('user_id', $user_id)->increment('money', $money);  // 自增

            $balance = DB::table('user_accounts')->where('user_id', $user_id)->select('money')->value('money');

            // 添加提现申请记录
            DB::table('cash_withdrawals')->insert([
                'money' => $money,
                'balance' => $balance,
                'user_id' => $user_id,
                'cash_withdrawal_account_id' => $request->input('cash_withdrawal_account_id'),
                'status' => 0,
            ]);
        }, 5);

        return redirect()->back()->with('success', '您的提现申请，已经提交成功！请耐心等待财务审核打款');
    }
}
