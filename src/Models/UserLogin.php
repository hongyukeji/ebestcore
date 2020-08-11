<?php

namespace System\Models;

class UserLogin extends Model
{
    protected $dates = [
        'login_time', 'last_login_time',
    ];
}
