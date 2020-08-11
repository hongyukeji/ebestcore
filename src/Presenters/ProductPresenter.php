<?php

namespace System\Presenters;

use System\Services\ProductService;

class ProductPresenter
{
    public function getBests($limit_number = null)
    {
        return (new ProductService())->getBests($limit_number);
    }

    public function getHots($limit_number = null)
    {
        return (new ProductService())->getHots($limit_number);
    }

    public function getNews($limit_number = null)
    {
        return (new ProductService())->getNews($limit_number);
    }
}
