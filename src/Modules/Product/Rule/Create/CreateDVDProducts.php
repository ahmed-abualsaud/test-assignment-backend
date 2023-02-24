<?php

namespace App\Modules\Product\Rule\Create;

use App\Setup\Rule;
use App\Utils\Helper;

class CreateDVDProducts extends Rule
{
    public function isApplicable($args)
    {
        $args = (array) $args;

        if (! empty($args) && array_key_exists("type", $args) && $args["type"] === "DVD") {
            return true;
        }

        return false;
    }

    public function apply($args)
    {
        $args = (array) $args;
        $repositories = $this->getRepositories();
        $args["type_id"] = $repositories["product_type"]->getProductTypeID("DVD");

        $entity = $repositories["product_entity"]->createProduct(Helper::array_only($args, ["sku", "name", "price", "type_id"]));
        $attribute = $repositories["product_attribute"]->getProductAttributes("size");

        $repositories["product_eav"]->createProductEAV([
            "product_entity_id" => $entity["id"],
            "product_attribute_id" => $attribute["id"],
            "attribute_value" => $args["size"]
        ]);

        $entity["size"] = $args["size"];
        return $entity;
    }
}
