<?php

if (!function_exists('return_result')) {
    /*
     * 统一结果返回响应
     *
     * 返回格式示例: $result = return_result()->success('example');
     *
     * 验证返回数据: echo return_result()->verify($result);
     */
    function return_result()
    {
        return new \System\Librarys\Supports\Result();
    }
}