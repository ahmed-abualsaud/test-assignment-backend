<?php

namespace App\Setup;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

use App\Setup\Config;
use App\Setup\Router;
use App\Setup\Routes;
use App\Setup\Container;

abstract class App
{
    public static function run()
    {
        Config::load(getcwd()."/.env");
        Routes::load();
        echo (Router::resolve(new Container(), strtolower($_SERVER["REQUEST_METHOD"]), $_SERVER["REQUEST_URI"]));
    }
}