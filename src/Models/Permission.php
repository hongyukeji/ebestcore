<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;
use System\Scopes\StatusScope;
use Illuminate\Database\Eloquent\Builder;
use Spatie\Permission\Models\Permission as BasePermission;
use System\Traits\Models\ModelTrait;

class Permission extends BasePermission
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
