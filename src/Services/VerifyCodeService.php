<?php

namespace System\Services;

use System\Models\Admin;
use System\Traits\Services\EmailVerifyCodeTrait;
use System\Traits\Services\SmsVerifyCodeTrait;
use Illuminate\Support\Facades\Cache;

class VerifyCodeService extends Service
{
    use SmsVerifyCodeTrait, EmailVerifyCodeTrait;

    public function saveVerifyCode($cache_prefix, $name, $verify_code, $minutes)
    {
        $key = $cache_prefix . $name;
        $value = $verify_code;
        Cache::put($key, $value, $minutes);
    }

    public function checkVerifyCode($cache_prefix, $name, $verify_code)
    {
        $key = $cache_prefix . $name;
        $cache_code = Cache::get($key);
        if ($cache_code == $verify_code) {
            return true;
        } else {
            return false;
        }
    }

    public function clearVerifyCode($cache_prefix, $name)
    {
        $key = $cache_prefix . $name;
        Cache::forget($key);
    }

    public function queryAdminUsername($username)
    {
        return is_mobile_number($username) ? Admin::query()->where('mobile', $username)->first() : Admin::query()->where('email', $username)->first();
    }
}
