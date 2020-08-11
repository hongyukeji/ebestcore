<?php

namespace System\Http\Controllers\Api\V1;

use System\Models\Category;
use System\Http\Resources\CategoryResource;
use System\Repository\Interfaces\CategoryInterface;

class CategoriesController extends Controller
{
    protected $category;

    public function __construct(CategoryInterface $category)
    {
        $this->category = $category;
    }

    public function index()
    {
        $categories = $this->category->findActiveAll();
        return CategoryResource::collection($categories)->additional(api_result(0));
    }

    public function tree()
    {
        $categories = $this->category->findTrees();
        return api_result(0, null, $categories);
    }

    public function show($id)
    {
        $category = $this->category->findOne($id);
        return $category ? api_result(0, null, new CategoryResource($category)) : abort(404);
    }
}
