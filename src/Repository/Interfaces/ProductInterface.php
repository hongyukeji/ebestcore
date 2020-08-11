<?php

namespace System\Repository\Interfaces;

interface ProductInterface
{
    public function findAll();

    public function findOne($id);

    public function search($shop_id = null);

    public function createCount($shop_id = null);
}
