<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Resources\ExampleResource;
use System\Models\Example;
use System\Repository\Interfaces\ExampleInterface;
use Illuminate\Http\Request;

class ExampleController extends Controller
{
    protected $example;

    public function __construct(ExampleInterface $example)
    {
        $this->example = $example;
    }

    public function index()
    {
        $examples = $this->example->findAll();
        return ExampleResource::collection($examples)->additional(api_result(0, null));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Example $example)
    {
        return $example ? new ExampleResource($example) : abort(404);
        //$example = $this->example->findOne($example->id) or abort(404);
        //return new ExampleResource($example);
    }

    public function edit(Example $example)
    {
        //
    }

    public function update(Request $request, Example $example)
    {
        //
    }

    public function destroy(Example $example)
    {
        //
    }
}
