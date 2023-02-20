<?php

namespace App\Modules\Product\Entity;

use App\Setup\Entity;

class ProductType extends Entity
{
    #Column[id]
    public $id;

    #Column[unique, string]
    public $type;
}