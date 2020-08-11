<?php

namespace System\Repository\Repositories;

use System\Models\Example;
use System\Repository\Interfaces\ExampleInterface;

class ExampleRepository extends Repository implements ExampleInterface
{
    public function findAll()
    {
        return Example::all();
    }

    public function findOne($id)
    {
        return Example::query()->find($id);
    }
}
