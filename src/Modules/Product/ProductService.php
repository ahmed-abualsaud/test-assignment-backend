<?php

namespace App\Modules\Product;

use App\Setup\RuleEngine;
use App\Modules\Product\Rule\Delete\DeleteProducts;
use App\Modules\Product\Rule\ListAll\ListAllProducts;
use App\Modules\Product\Rule\Create\CreateDVDProducts;
use App\Modules\Product\Rule\Create\CreateBookProducts;
use App\Modules\Product\Rule\Create\CreateFurnitureProducts;

class ProductService
{
    private $repository;

    public function __construct(ProductRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list()
    {
        return RuleEngine::run([
            new ListAllProducts($this->repository)
        ]);
    }

    public function create($args)
    {
        return RuleEngine::run([
            new CreateDVDProducts($this->repository),
            new CreateBookProducts($this->repository),
            new CreateFurnitureProducts($this->repository)
        ], $args);
    }

    public function delete($ids)
    {
        return RuleEngine::run([
            new DeleteProducts($this->repository)
        ], $ids);
    }
}