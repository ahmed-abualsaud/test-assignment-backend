<?php

namespace App\Modules\Product\DTO;

use App\Setup\DTO;

class CreateProductDTO extends DTO
{
    #Rules(required, string)
    public $sku;

    #Rules(required, string)
    public $name;

    #Rules(required, numeric)
    public $price;
}