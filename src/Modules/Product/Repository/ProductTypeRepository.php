<?php

namespace App\Modules\Product\Repository;

use App\Setup\Database;
use App\Modules\Product\Entity\ProductType;

class ProductTypeRepository
{
    private $repository;

    public function __construct()
    {
        $this->repository = Database::getRepository(ProductType::class);
    }

    public function getProductTypeID($type)
    {
        return $this->repository->getWhere(["type" => $type], "id")[0]["id"];
    }
}