<?php

namespace App\Modules\Product;

use App\Modules\Product\DTO\CreateProductDTO;

class ProductController {
    public function list(CreateProductDTO $args)
    {
        return json_encode($args);
    }

    public function create(CreateProductDTO $args)
    {
        return json_encode($args); 
    }
}