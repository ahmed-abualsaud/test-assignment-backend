<?php

require __DIR__ . '/vendor/autoload.php';

use App\Setup\Container;
use App\Setup\App;
use App\Setup\Router;
use App\Config\ConfigService;
use App\Database\Entity;
use App\Modules\Product\ProductEntity;
use App\Modules\Product\DTO\CreateProductDTO;

ConfigService::load(".env");

//echo HTTPResponse::error(getenv("DATABASE_PORT"));
//echo [$_POST["ssaa"]];
//echo (new ProductEntity())->getTokens();
//print_r((new Container())->get(ProductEntity::class));
//echo is_subclass_of(new ProductEntity(), Entity::class);
//print_r((new CreateProductDTO($_POST)));


Router::get("/", [\App\Modules\Product\ProductController::class, "list"]);
Router::post("/create", [\App\Modules\Product\ProductController::class, "create"]);

App::run();
