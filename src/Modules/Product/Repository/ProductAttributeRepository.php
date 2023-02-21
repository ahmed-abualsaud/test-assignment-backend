<?php

namespace App\Modules\Product\Repository;

use App\Setup\Database;
use App\Setup\DBQuery;
use App\Modules\Product\Entity\ProductAttribute;

class ProductAttributeRepository
{
    private $repository;

    public function __construct()
    {
        $this->repository = Database::getRepository(ProductAttribute::class);
    }

    public function getAllProductAttributes(...$select)
    {
        return $this->repository->all(...$select);
    }

    public function getProductAttributes($attributes)
    {
        if (is_array($attributes)) {
            $attributes = $this->repository->getWhere(["attribute_name" => DBQuery::whereIn($attributes)]);

            $newAttr = [];
            array_map(
                function($element) use (&$newAttr) {
                    $newAttr[$element["attribute_name"]] = $element["id"];
                },
                $attributes
            );
    
            return $newAttr;
        }

        return $this->repository->getWhere(["attribute_name" => $attributes], "id", "attribute_type")[0];
    }
}