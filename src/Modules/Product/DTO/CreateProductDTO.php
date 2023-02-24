<?php

namespace App\Modules\Product\DTO;

use App\Setup\DTO;

class CreateProductDTO extends DTO
{
    #Rules[unique(App\Modules\Product\Entity\ProductEntity), required, string]
    public $sku;

    #Rules[required, string]
    public $name;

    #Rules[required, positive]
    public $price;

    #Rules[required, string]
    public $type;

    #Rules[required_when(type=DVD), positive]
    public $size;

    #Rules[required_when(type=Book), positive, notzero]
    public $weight;

    #Rules[required_when(type=Furniture), positive, notzero]
    public $height;

    #Rules[required_when(type=Furniture), positive, notzero]
    public $width;

    #Rules[required_when(type=Furniture), positive, notzero]
    public $length;
}