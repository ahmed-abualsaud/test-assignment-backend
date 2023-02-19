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
        return $this->getRepository()->createProduct(Helper::array_only($args, ["sku", "name", "price"]));
    }
}
