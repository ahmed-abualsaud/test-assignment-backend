<?php

namespace App\Modules\Product\Entity;

use App\Setup\Entity;

class ProductEAV extends Entity
{
    #Column[id]
    public $id;

    #Column[bigint]
    public $product_entity_id;

    #Column[bigint]
    public $product_attribute_id;

    #Column[string]
    public $attribute_value;
}