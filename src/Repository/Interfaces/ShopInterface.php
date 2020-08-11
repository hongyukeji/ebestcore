<?php

namespace System\Repository\Interfaces;

interface ShopInterface
{
    public function findAll();

    public function findOne($id);

    public function search();
}
