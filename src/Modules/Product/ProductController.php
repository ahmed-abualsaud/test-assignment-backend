<?php

namespace App\Modules\Product;

use App\Modules\Product\DTO\CreateProductDTO;

class ProductController {

    protected $productService;

    public function __construct(ProductService $productService) 
    {
        $this->productService = $productService;
    }


    public function list()
    {
        return $this->productService->list();
    }

    public function create(CreateProductDTO $args)
    {
        return $this->productService->create($args);
    }

    public function delete($ids)
    {
        return $this->productService->delete($ids);
    }
}