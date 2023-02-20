<?php

namespace App\Modules\Product\Repository;

use App\Setup\Database;
use App\Modules\Product\Entity\ProductAttribute;

class ProductAttributeRepository
{
    private $repository;

    public function __construct()
    {
        $this->repository = Database::getRepository(ProductAttribute::class);
    }

    public function getProductAttributes($attributes)
    {
        if (is_array($attributes)) {

        }

        return $this->repository->getWhere(["attribute_name" => $attributes], "id", "attribute_type")[0];
    }
}