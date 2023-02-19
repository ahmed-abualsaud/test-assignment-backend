<?php

namespace App\Modules\Product;

use App\Setup\Database;
class ProductRepository
{
    private $repository;

    public function __construct()
    {
        $this->repository = Database::getRepository(ProductEntity::class);
    }
    
    public function getAllProducts()
    {
        return $this->repository->all();
    }

    public function createProduct($args)
    {
        return $this->repository->create($args);
    }

    public function deleteProducts($ids)
    {
        return $this->repository->delete($ids);
    }
}