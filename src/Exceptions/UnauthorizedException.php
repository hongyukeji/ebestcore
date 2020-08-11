<?php

namespace System\Exceptions;

use Exception;

class UnauthorizedException extends Exception
{
    public function render($request)
    {
        $status_code = $this->getCode();
        $message = $this->getMessage();

        if (request()->expectsJson()) {
            return ['status_code' => $status_code, 'message' => $message];
        }

        return view('backend::errors.404')->with(['status_code' => $status_code, 'message' => $message]);
    }
}