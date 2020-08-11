<?php

namespace System\Utils\Util;

use ReflectionClass;

/**
 *
 * 工具类，使用该类来实现自动依赖注入。
 *
 * @use \App\Facades\Util::tree()->generate([]);
 *
 * @use self::getInstance("App\\Utils\\Util\\Expands\\Example");
 * @use self::make("App\\Utils\\Util\\Libs\\Example", 'hello', ['Nick']);
 *
 */
class Util
{
    function __call($className, $arguments)
    {
        $className = __NAMESPACE__ . "\\Libs\\" . ucwords($className);
        $class = new ReflectionClass($className);
        return $class->newInstanceArgs($arguments);
    }
}
