<?php

namespace System\Presenters;

class BackendPresenter
{
    /*
     * 服务器信息
     */
    public function getSystemInfo()
    {
        $systems = [
            ['name' => trans('backend.pages.dashboard.system'), 'value' => config('app.system_name')],
            ['name' => trans('backend.pages.dashboard.version'), 'value' => config('app.version')],
            ['name' => trans('backend.pages.dashboard.author'), 'value' => config('app.author')],
            ['name' => trans('backend.pages.dashboard.support'), 'value' => config('app.support')],
            ['name' => trans('backend.pages.dashboard.url'), 'value' => config('app.url')],
            [
                'name' => trans('backend.pages.dashboard.debug'),
                'value' => (boolean)config('app.debug') ? trans('backend.commons.status_open') : trans('backend.commons.status_close')
            ],
            ['name' => trans('backend.pages.dashboard.env'), 'value' => config('app.env')],
            ['name' => trans('backend.pages.dashboard.locale'), 'value' => config('app.locale')],
            ['name' => trans('backend.pages.dashboard.timezone'), 'value' => config('app.timezone')],
            ['name' => trans('backend.pages.dashboard.cache_driver'), 'value' => config('cache.default')],
            ['name' => trans('backend.pages.dashboard.queue_driver'), 'value' => config('queue.default')],
            ['name' => trans('backend.pages.dashboard.session_driver'), 'value' => config('session.driver')],
        ];

        return $systems;
    }

    public function getEnvInfo()
    {
        $envs = [
            ['name' => trans('backend.pages.dashboard.web_server'), 'value' => array_get($_SERVER, 'SERVER_SOFTWARE')],
            ['name' => trans('backend.pages.dashboard.php_version'), 'value' => PHP_VERSION],
            ['name' => trans('backend.pages.dashboard.php_cgi'), 'value' => php_sapi_name()],
            ['name' => trans('backend.pages.dashboard.mysql_version'), 'value' => get_mysql_server_info()],
            [
                'name' => trans('backend.pages.dashboard.server_ip'),
                'value' => request()->server('SERVER_ADDR') ?: '127.0.0.1'
            ],
            ['name' => trans('backend.pages.dashboard.server_domain'), 'value' => get_server_url()],
            [
                'name' => trans('backend.pages.dashboard.server_system'),
                'value' => php_uname('s') . '（' . php_uname('r') . '）'
            ],    // php_uname()
            ['name' => trans('backend.pages.dashboard.server_port'), 'value' => $_SERVER['SERVER_PORT']],
            [
                'name' => trans('backend.pages.dashboard.safe_mode'),
                'value' => (boolean)ini_get('safe_mode')
                    ? trans('backend.commons.status_open')
                    : trans('backend.commons.status_close') // Enable/Disable
            ],
            [
                'name' => trans('backend.pages.dashboard.upload_max_filesize'),
                'value' => ini_get("file_uploads") ? ini_get("upload_max_filesize") : "Disabled"
            ],
            [
                'name' => trans('backend.pages.dashboard.upload_max_ex_time'),
                'value' => ini_get("max_execution_time") . "秒"
            ],
            ['name' => trans('backend.pages.dashboard.laravel_version'), 'value' => app()->version()],
        ];

        return $envs;
    }
}
