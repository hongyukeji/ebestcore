<?php

namespace System\Repository\Interfaces;

interface RegionInterface
{
    public function findAll();

    public function findOne($id);

    public function findActiveAll();

    public function findTrees();

    public function findActiveTrees();
}
