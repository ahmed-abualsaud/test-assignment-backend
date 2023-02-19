<?php

namespace App\Setup;

abstract class Routes
{
    public static function load()
    {
        Router::get("/", [\App\Modules\Product\ProductController::class, "list"]);
        Router::post("/add-product", [\App\Modules\Product\ProductController::class, "create"]);
        Router::delete("/delete-products", [\App\Modules\Product\ProductController::class, "delete"]);
    }
}