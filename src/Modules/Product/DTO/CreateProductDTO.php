<?php

namespace App\Modules\Product\DTO;

use App\Setup\DTO;

class CreateProductDTO extends DTO
{
    #Rules[unique(App\Modules\Product\Entity\ProductEntity), required, string]
    public $sku;

    #Rules[required, string]
    public $name;

    #Rules[required, numeric]
    public $price;

    #Rules[required, string]
    public $type;

    #Rules[required_when(type=DVD-disc), numeric]
    public $size;

    #Rules[required_when(type=Book), numeric]
    public $weight;

    #Rules[required_when(type=Furniture), numeric]
    public $height;

    #Rules[required_when(type=Furniture), numeric]
    public $width;

    #Rules[required_when(type=Furniture), numeric]
    public $length;
}