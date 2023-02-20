<?php

namespace App\Modules\Product\Rule\Create;

use App\Setup\Rule;
use App\Utils\Helper;

class CreateFurnitureProducts extends Rule
{
    public function isApplicable($args)
    {
        $args = (array) $args;

        if (! empty($args) && array_key_exists("type", $args) && $args["type"] === "Furniture") {
            return true;
        }

        return false;
    }

    public function apply($args)
    {
        $args = (array) $args;
        $repositories = $this->getRepositories();
        $args["type_id"] = $repositories["product_type"]->getProductTypeID("Furniture");

        $entity = $repositories["product_entity"]->createProduct(Helper::array_only($args, ["sku", "name", "price", "type_id"]));
        $attributes["height"] = $repositories["product_attribute"]->getProductAttributes(["height", "width", "length"]);

        $repositories["product_eav"]->createProductEAV(
            [
                "product_entity_id" => $entity["id"],
                "product_attribute_id" => $attributes["height"]["id"],
                "attribute_value" => $args["height"]
            ],
            [
                "product_entity_id" => $entity["id"],
                "product_attribute_id" => $attributes["width"]["id"],
                "attribute_value" => $args["width"]
            ],
            [
                "product_entity_id" => $entity["id"],
                "product_attribute_id" => $attributes["length"]["id"],
                "attribute_value" => $args["length"]
            ]
        );

        $entity["height"] = $args["height"];
        $entity["width"] = $args["width"];
        $entity["length"] = $args["length"];
        return $entity;
    }
}
