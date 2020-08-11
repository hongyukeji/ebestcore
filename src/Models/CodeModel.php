<?php

namespace System\Models;

class CodeModel extends Model
{
    const STATUS_ACTIVE = [1, "Active"];

    const STATUS_INACTIVE = [0, "Inactive"];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }
}
