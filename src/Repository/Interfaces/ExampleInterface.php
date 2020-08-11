<?php

namespace System\Repository\Interfaces;

interface ExampleInterface
{
    public function findAll();

    public function findOne($id);
}