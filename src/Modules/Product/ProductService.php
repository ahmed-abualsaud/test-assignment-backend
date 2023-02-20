<?php

namespace App\Modules\Product;

use App\Modules\Product\Repository\ProductEAVRepository;
use App\Setup\RuleEngine;
use App\Modules\Product\Rule\Delete\DeleteProducts;
use App\Modules\Product\Rule\ListAll\ListAllProducts;
use App\Modules\Product\Rule\Create\CreateDVDProducts;
use App\Modules\Product\Rule\Create\CreateBookProducts;
use App\Modules\Product\Rule\Create\CreateFurnitureProducts;

use App\Modules\Product\Repository\ProductTypeRepository;
use App\Modules\Product\Repository\ProductEntityRepository;
use App\Modules\Product\Repository\ProductAttributeRepository;


class ProductService
{
    private $repositories;

    public function __construct(
        ProductEAVRepository $productEAVRepository,
        ProductTypeRepository $productTypeRepository,
        ProductEntityRepository $productEntityRepository,
        ProductAttributeRepository $productAttributeRepository
    ) {
        $this->repositories["product_eav"] = $productEAVRepository;
        $this->repositories["product_type"] = $productTypeRepository;
        $this->repositories["product_entity"] = $productEntityRepository;
        $this->repositories["product_attribute"] = $productAttributeRepository;
    }

    public function list()
    {
        return RuleEngine::run([
            new ListAllProducts($this->repositories)
        ]);
    }

    public function create($args)
    {
        return RuleEngine::run([
            new CreateDVDProducts($this->repositories),
            new CreateBookProducts($this->repositories),
            new CreateFurnitureProducts($this->repositories)
        ], $args);
    }

    public function delete($ids)
    {
        return RuleEngine::run([
            new DeleteProducts($this->repositories)
        ], $ids);
    }
}