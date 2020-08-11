<?php

namespace System\Repository\Interfaces;

interface BrandInterface
{
    public function findAll();

    public function findOne($id);

    public function search();
}
