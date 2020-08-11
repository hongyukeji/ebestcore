<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;
use Spatie\Permission\Models\Role as BaseRole;
use System\Traits\Models\ModelTrait;

class Role extends BaseRole
{
    use ModelTrait, CategoryTrait;

    public function __construct(array $attributes = [])
    {
        $attributes['guard_name'] = $attributes['guard_name'] ?? "admin";

        parent::__construct($attributes);
    }

    public function getGroupList()
    {
        return $this->query()->sorted()->get()->groupBy('group');
    }
}
