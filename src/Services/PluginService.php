<?php

namespace System\Services;

use Hongyukeji\Plugin\Loader;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PluginService extends Service
{
    protected $loader;

    public function __construct()
    {
        $this->loader = Loader::forge()->addDir($this->getPluginsDir());
    }

    /*
     * 获取所有插件列表
     */
    public function getPlugins()
    {
        return $this->loader->getAll();
    }

    /*
     * 获取单个插件实例
     */
    public function getPlugin($plugin_name)
    {
        return $this->loader->get($plugin_name);
    }

    /*
     * 启用插件
     */
    public function enable($plugin_name)
    {
        $plugin = $this->getPlugin($plugin_name);
        $plugin->setConfig(['extra' => ['status' => true]]);
        if ($plugin->getConfig('extra.auto_install') && !$plugin->getConfig('extra.install')) {
            $plugin->install();
            $plugin->setConfig(['extra' => ['install' => true]]);
        }
    }

    /*
     * 关闭插件
     */
    public function disable($plugin_name)
    {
        $plugin = $this->getPlugin($plugin_name);
        $plugin->setConfig(['extra' => ['status' => false]]);
    }

    /*
     * 安装插件
     */
    public function install($plugin_name)
    {
        $plugin = $this->getPlugin($plugin_name);
        $plugin->install();
        $plugin->setConfig(['extra' => ['install' => true]]);
    }

    /*
     * 卸载插件
     */
    public function uninstall($plugin_name)
    {
        $plugin = $this->getPlugin($plugin_name);
        $plugin->uninstall();
        $plugin->setConfig(['extra' => ['install' => false, 'status' => false]]);
    }

    /*
     * 删除插件
     */
    public function delete($plugin_name)
    {
        /*try {
            $plugin = $this->getPlugin($plugin_name);
            $plugin->remove();
            return true;
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
            return false;
        }*/
        $plugin = $this->getPlugin($plugin_name);
        $plugin->uninstall();
        $plugin->remove();
    }

    /*
     * 获取启用插件列表
     */
    public function getEnabledPlugins()
    {
        //
    }

    /*
     * 判断插件是否启用
     */
    public function isEnabled($plugin_name)
    {
        $plugin = $this->getPlugin($plugin_name);
        return boolval($plugin->getConfig('extra.status'));
    }

    /*
     * 获取插件目录
     */
    public function getPluginsDir()
    {
        return config('plugins.directory', base_path('plugins'));
    }
}
