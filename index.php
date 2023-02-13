<?php

require __DIR__ . '/vendor/autoload.php';

use App\Setup\App;
use App\Setup\Router;
use App\Config\ConfigService;

ConfigService::load(".env");

//echo HTTPResponse::error(getenv("DATABASE_PORT"));
//echo print_r($_SERVER);

Router::get("/", [\App\Modules\Product\ProductController::class, "list"]);
Router::post("/create", [\App\Modules\Product\ProductController::class, "create"]);

App::run();
?>