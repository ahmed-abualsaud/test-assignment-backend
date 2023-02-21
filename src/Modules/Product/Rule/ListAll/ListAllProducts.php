<?php

namespace App\Modules\Product\Rule\ListAll;

use App\Setup\Rule;

class ListAllProducts extends Rule
{
    public function isApplicable($args)
    {
        return true;
    }

    public function apply($args)
    {
        $repositories = $this->getRepositories();
        $attributes = $repositories["product_attribute"]->getAllProductAttributes("attribute_name");
        return $repositories["product_entity"]->getAllProducts($attributes);
    }
}