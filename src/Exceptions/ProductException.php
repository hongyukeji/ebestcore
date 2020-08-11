<?php

namespace System\Exceptions;

use Exception;

class ProductException extends Exception
{
    public function render($request)
    {
        $status_code = $this->getCode();
        $message = $this->getMessage();

        if (request()->expectsJson()) {
            return ['status_code' => $status_code, 'message' => $message];
        }

        return view("frontend::errors.{$status_code}")->with(['status_code' => $status_code, 'message' => $message]);
    }
}
