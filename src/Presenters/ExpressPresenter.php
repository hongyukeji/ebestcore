<?php

namespace System\Presenters;

class ExpressPresenter
{
    public function get($express_no, $express_name)
    {
        if (!class_exists('\Hongyukeji\ExpressKdniao\Services\KdNiaoService')) {
            return null;
        }

        $param = [
            'express_no' => $express_no,
            'express_name' => $express_name,
        ];

        $KdNiaoPlugin = new \Hongyukeji\ExpressKdniao\Services\KdNiaoService();
        $result = $KdNiaoPlugin->query($param);
        return $result['data'] ?? null;
    }
}
