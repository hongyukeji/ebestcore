<?php
/**
 * 全局函数
 */

/*
 * 自动包含当前目录下所有php文件
 */
foreach (array_diff(scandir(dirname(__FILE__)), ['.', '..', basename(__FILE__)]) as $file) {
    include_once dirname(__FILE__) . DIRECTORY_SEPARATOR . "$file";
}

/*
 * 自动包含app/Helpers目录下所有php文件
 */
/*$app_helpers_path = dirname(dirname(dirname(__FILE__))) . '/app/Helpers';
foreach (array_diff(scandir($app_helpers_path), ['.', '..']) as $file) {
    include_once $app_helpers_path . DIRECTORY_SEPARATOR . "$file";
}*/
