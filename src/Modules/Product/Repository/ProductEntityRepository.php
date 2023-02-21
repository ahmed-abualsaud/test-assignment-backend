<?php

namespace App\Modules\Product\Repository;

use App\Setup\DBQuery;
use App\Setup\Database;
use App\Modules\Product\Entity\ProductEntity;

class ProductEntityRepository
{
    private $repository;

    public function __construct()
    {
        $this->repository = Database::getRepository(ProductEntity::class);
    }
    
    public function getAllProducts()
    {
        return $this->repository->getWhereInnerJoin(
            [
                [
                    "product_types",
                    "id",
                    "type_id"
                ]
            ], 
            []
        );
    }

    public function createProduct($args)
    {
        return $this->repository->create($args);
    }

    public function deleteProducts($ids)
    {
        $ids = preg_split("/[\s,]+/", trim($ids, "[]"));

        return $this->repository->delete(["id" => DBQuery::whereIn($ids)]);
    }
}