<?php

namespace App\Modules\Product;

class ProductService
{
    public function list()
    {
        return json_encode(["name" => "ahmed", "sku" => "123-456-789"]);
    }

    public function create($args)
    {
        return json_encode($args); 
    }

    public function delete($ids)
    {
        return json_encode($ids); 
    }
}