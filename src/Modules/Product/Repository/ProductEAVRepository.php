<?php

namespace App\Modules\Product\Repository;

use App\Setup\Database;
use App\Modules\Product\Entity\ProductEAV;

class ProductEAVRepository
{
    private $repository;

    public function __construct()
    {
        $this->repository = Database::getRepository(ProductEAV::class);
    }

    public function createProductEAV($args)
    {
        return $this->repository->create($args);

    }
}