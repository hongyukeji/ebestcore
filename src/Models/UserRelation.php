<?php

namespace System\Models;

class UserRelation extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
            $user->name = '';
        });
    }
}
