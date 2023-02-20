<?php

namespace App\Modules\Product\Entity;

use App\Setup\Entity;

class ProductEntity extends Entity
{
    #Column[id]
    public $id;

    #Column[string, unique]
    public $sku;

    #Column[string]
    public $name;
  
    #Column[float]
    public $price;

    #Column[bigint]
    public $type_id;
}