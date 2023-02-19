<?php

namespace App\Modules\Product;

use App\Setup\Entity;

class ProductEntity extends Entity
{
    #Column[id]
    public $id;

    #Column[string, unique]
    public $sku;

    #Column[string]
    public $name;
  
    #Column[numeric]
    public $price;
}