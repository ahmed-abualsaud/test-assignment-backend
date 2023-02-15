<?php

namespace App\Setup;

use App\Setup\Router;
use App\Setup\Container;

abstract class App
{
    public static function run()
    {
        $container = new Container();
        echo (Router::resolve($container, strtolower($_SERVER["REQUEST_METHOD"]), $_SERVER["REQUEST_URI"]));
    }
}