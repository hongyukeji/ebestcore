<?php

namespace System\Utils\Util\Libs;

class ArrayTree
{
    /**
     * 将传入的数组, 生成树结构
     *
     * @param $array
     * @param string $parent_key
     * @param string $children_key
     * @return array
     */
    public function generate($array, $parent_key = 'parent_id', $children_key = 'children')
    {
        $map = [];
        $res = [];
        foreach ($array as $id => &$item) {
            // 获取出每一条数据的父id
            $parent_id = &$item[$parent_key];
            // 将每一个item的引用保存到$map中
            $map[$item['id']] = &$item;
            // 如果在map中没有设置过他的parent_id, 说明是根节点, parent_id为0,
            if (!isset($map[$parent_id])) {
                // 将parent_id为0的item的引用保存到$res中
                $res[$id] = &$item;
            } else {
                // 如果在map中没有设置过他的parent_id, 则将该item加入到他父亲的叶子节点中
                $pItem = &$map[$parent_id];
                $pItem[$children_key][] = &$item;
            }
        }
        return $res;
    }
}