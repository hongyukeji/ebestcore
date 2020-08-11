<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;

class Menu extends Model
{
    use CategoryTrait;

    /*
     * 可以被批量赋值的属性。
     */
    //protected $fillable = [];

    /*
     * 不可被批量赋值的属性。
     */
    protected $guarded = [];

    public function purview()
    {
        return $this->hasOne(Permission::class, 'name', 'permission');
    }
}
