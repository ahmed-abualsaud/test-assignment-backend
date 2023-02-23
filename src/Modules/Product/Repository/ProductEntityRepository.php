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
    
    public function getAllProducts($attributes)
    {
        $pivots = "";
        foreach ($attributes as $attribute) {
            $pivots .= " MAX(CASE WHEN attribute_name = \"".$attribute["attribute_name"]."\" THEN attribute_value END) AS ".$attribute["attribute_name"].",";
        }
        $pivots = substr($pivots, 0, strlen($pivots) - 1);

        $data = $this->repository->executeReadQuery("
            SELECT *
            FROM (
            SELECT product_id, sku, name, price, type_id,".$pivots."
            FROM (
                SELECT product_attributes.attribute_name, product_eavs.attribute_value, product_entities.id as product_id,
                product_entities.sku, product_entities.name, product_entities.price, product_entities.type_id
                FROM product_eavs
                INNER JOIN product_entities ON product_eavs.product_entity_id=product_entities.id
                INNER JOIN product_attributes ON product_eavs.product_attribute_id=product_attributes.id
            ) AS atts GROUP BY product_id
            ) AS myquery
            INNER JOIN product_types ON myquery.type_id=product_types.id
            ORDER BY product_id
        ");

        foreach ($data as $index => $rows) {
            foreach ($rows as $column => $value) {
                if ($rows[$column] == null) {
                    unset($data[$index][$column]);
                }
            }
            $data[$index]['id'] = (int) $data[$index]['product_id'];
            unset($data[$index]['product_id']);
        }

        if (count($data) === 1) {
            $data = [$data];
        }

        return $data;
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