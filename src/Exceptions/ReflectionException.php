<?php

namespace System\Exceptions;

use Exception;
use Illuminate\Support\Facades\Artisan;

class ReflectionException extends Exception
{
    public function render($request)
    {
        $status_code = $this->getCode();
        $message = $this->getMessage();

        Artisan::call('composer:autoload');

        if (request()->expectsJson()) {
            return ['status_code' => $status_code, 'message' => $message];
        }

        return view("frontend::errors.{$status_code}")->with(['status_code' => $status_code, 'message' => $message]);
    }
}
