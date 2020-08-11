<?php

namespace System\Repository\Interfaces;

interface UserInterface
{
    public function findAll();

    public function findOne($id);

    public function createCount();

    public function todayCreateCount();

    public function yesterdayCreateCount();
}