<?php

namespace System\Http\Controllers\Api\V1\WeChat;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use System\Http\Controllers\Api\Controller;

class OpenIdController extends Controller
{
    /*
     * 获取 openid
     * TODO: 待验证
     */
    public function index(Request $request)
    {
        $appid = '';
        $secret = '';
        $callback_url = $request->input('callback_url');
        $code = $request->input('code');  # redirect_uri/?code=CODE&state=STATE

        // 判断是否在微信中
        $ua = $request->header('User-Agent');
        $is_in_wechat = preg_match('/MicroMessenger/i', $ua);

        if ($is_in_wechat) {
            // 用户在微信中
            if ($code) {
                // 通过 code 获取 open_id
                $client = new Client();
                $url = sprintf("https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code",
                    $appid, $secret, $code);
                try {
                    $res = $client->request('GET', $url, ['timeout' => 1.5]);
                    $res = $res->getBody();
                    $res = json_decode($res);
                    $open_id = $res->openid;
                    if (strpos($callback_url, '?')) {
                        $url = $callback_url . http_build_query(['openid' => $open_id]);
                    } else {
                        $url = $callback_url . '?' . http_build_query(['openid' => $open_id]);
                    }
                    return redirect($url);
                } catch (\Exception $e) {
                    Log::info('Fail to call api');
                }
            } else {
                // 静默授权，跳转获取 code
                $url = sprintf("https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=snsapi_base#wechat_redirect",
                    $appid, urlencode($request->getUri()));
                return redirect($url);
            }
        }
    }
}
