<?php

namespace App\Setup;

use App\Setup\Router;
use App\Setup\Routes;
use App\Setup\Container;

abstract class App
{
    public static function run()
    {
        Routes::load();
        echo (Router::resolve(new Container(), strtolower($_SERVER["REQUEST_METHOD"]), $_SERVER["REQUEST_URI"]));
    }
}