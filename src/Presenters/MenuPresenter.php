<?php

namespace System\Presenters;

use System\Models\Menu;
use System\Repository\Interfaces\MenuInterface;

class MenuPresenter
{
    protected $menu;

    public function __construct(MenuInterface $menu)
    {
        $this->menu = $menu;
    }

    public function getTrees()
    {
        return $this->menu->findTrees();
    }

    public function getActiveTrees()
    {
        $menus = (new Menu)->getTrees();
        return $menus;
    }
}
