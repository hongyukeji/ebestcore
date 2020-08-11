<?php

namespace System\Http\Controllers\Frontend;

use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $options = [
            'uid' => $request->input('uid', 'shadow'),
            'appkey' => $request->input('appkey', config("websites.basic.im.app_key")),
            'credential' => $request->input('credential', '123456'),
            'touid' => $request->input('to_uid', 'imuser123'),
        ];
        //$options['sendMsgToCustomService'] = true;

        // 判断是否是登录用户

        // 会员 - 获取im相关信息 不存在im信息则创建
        return view('frontend::chat.index', compact('options'));
    }
}
