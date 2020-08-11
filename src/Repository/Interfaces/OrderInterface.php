<?php

namespace System\Repository\Interfaces;

interface OrderInterface
{
    public function findAll();

    public function findOne($id);

    public function createCount($shop_id = null);

    public function getIncomes($shop_id = null);

    public function getDailyIncome($shop_id = null);
}
