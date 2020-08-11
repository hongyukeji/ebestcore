<?php

namespace System\Repository\Repositories;

use System\Models\User;
use Illuminate\Support\Carbon;
use System\Repository\Interfaces\UserInterface;
use System\Traits\Repository\CountRepositoryTrait;

class UserRepository extends Repository implements UserInterface
{
    use CountRepositoryTrait;

    public function createCountModel()
    {
        return User::query();
    }

    public function findAll()
    {
        return User::all();
    }

    public function findOne($id)
    {
        return User::query()->find($id);
    }

    public function todayCreateCount()
    {
        return User::query()->whereDate('created_at', Carbon::today())->count();
    }

    public function yesterdayCreateCount()
    {
        return User::query()->whereDate('created_at', Carbon::yesterday())->count();
    }
}
