<?php

namespace App\Setup;

use App\Exceptions\RouteNotFoundException;

abstract class Router
{
    private static $routes = [];

    public function getRoutes()
    {
        return $this->routes;
    }

    private static function register(string $requestMethod, string $route, $action)
    {
        self::$routes[$requestMethod][$route] = $action;
    }

    public static function get(string $route, $action)
    {
        self::$routes["get"][$route] = $action;
    }

    public static function post(string $route, $action)
    {
        self::$routes["post"][$route] = $action;
    }

    public static function resolve(string $requestMethod, string $requestUri)
    {
        $route = explode('?', $requestUri)[0];
        $action = self::$routes[$requestMethod][$route]?? null;

        if (! $action) {
            throw new RouteNotFoundException();
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }

        if (is_array($action)) {
            $class = $action[0];
            $method = $action[1];

            if (class_exists($class)) {
                $class = new $class();

                if (method_exists($class, $method)) {
                    return call_user_func_array([$class, $method], []);
                }

                throw new RouteNotFoundException("Method '".$method."' does not exists inside class '".$class."'");
            }

            throw new RouteNotFoundException("Class '".$class."' Does not exists");
        }
    }
}