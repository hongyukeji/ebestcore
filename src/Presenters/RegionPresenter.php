<?php

namespace System\Presenters;

use System\Repository\Interfaces\RegionInterface;

class RegionPresenter
{
    protected $region;

    public function __construct(RegionInterface $region)
    {
        $this->region = $region;
    }

    public function getTrees()
    {
        return $this->region->findTrees();
    }

    public function getActiveTrees()
    {
        return $this->region->findActiveTrees();
    }
}
