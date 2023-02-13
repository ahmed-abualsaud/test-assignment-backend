<?php

namespace App\Setup;

use App\Setup\Router;
abstract class App
{
    public static function run()
    {
        echo Router::resolve(strtolower($_SERVER["REQUEST_METHOD"]), $_SERVER["REQUEST_URI"]);
    }
}