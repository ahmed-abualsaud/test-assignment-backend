<?php

namespace App\Setup;

abstract class Routes
{
    public static function load()
    {
        Router::get("/api/", [\App\Modules\Product\ProductController::class, "list"]);
        Router::post("/api/add-product", [\App\Modules\Product\ProductController::class, "create"]);
        Router::delete("/api/delete-products", [\App\Modules\Product\ProductController::class, "delete"]);
    }
}