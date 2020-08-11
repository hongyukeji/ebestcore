<?php

namespace System\Presenters;

use System\Repository\Interfaces\CategoryInterface;

class CategoryPresenter
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }

    public function getTrees()
    {
        return $this->category->findTrees();
    }

    public function getActiveTrees()
    {
        return $this->category->findActiveTrees();
    }
}