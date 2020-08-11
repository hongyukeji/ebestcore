<?php

namespace System\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Cache;
use System\Services\VerifyCodeService;

class MobileVerifyCodeRule implements Rule
{
    public $mobile;

    /**
     * Create a new rule instance.
     *
     * MobileVerifyCodeRule constructor.
     * @param $mobile
     */
    public function __construct($mobile)
    {
        $this->mobile = $mobile;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $verifyCodeService = new VerifyCodeService();
        $verify_code_key_prefix = config('params.cache.backend.verify_code.prefix', 'backend_verify_code_');
        return $verifyCodeService->checkVerifyCode($verify_code_key_prefix, $this->mobile, $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('validation.mobile_verify_code');
    }
}
