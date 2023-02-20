<?php

namespace App\Modules\Product\Entity;

use App\Setup\Entity;

class ProductAttribute extends Entity
{
    #Column[id]
    public $id;

    #Column[unique, string]
    public $attribute_name;

    #Column[string]
    public $attribute_type;
}