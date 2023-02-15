<?php

namespace App\Setup;

use ReflectionObject;
use App\Setup\Container;
use App\Utils\HTTPResponse;
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

    public static function resolve(Container $container, string $requestMethod, string $requestUri)
    {
        $route = explode('?', $requestUri)[0];
        $action = self::$routes[$requestMethod][$route]?? null;

        if (! $action) {
            throw new RouteNotFoundException();
        }

        if (is_array($action)) {
            $class = $action[0];
            $method = $action[1];

            if (class_exists($class)) {
                $class = new $class();

                if (method_exists($class, $method)) {

                    if (strtolower($requestMethod) == "get") {
                        return self::validateParamsAndCall($container, $class, $method, $_GET);
                    }
                    
                    if (strtolower($requestMethod) == "post") {
                        return self::validateParamsAndCall($container, $class, $method, $_POST);
                    }
                }
                throw new RouteNotFoundException("Method '".$method."' does not exists inside class '".$class."'");
            }
            throw new RouteNotFoundException("Class '".$class."' does not exists");
        }

        if (is_callable($action)) {
            return call_user_func($action);
        }
    }

    private static function validateParamsAndCall($container, $class, $method, $args)
    {
        $object = new ReflectionObject($class);
        $parameters = $object->getMethod($method)->getParameters();
        $newParams = [];

        foreach($parameters as $parameter) {

            $parameterClass = (string) $parameter->getType();
            if ($parameter->getType() && is_subclass_of($parameterClass, DTO::class)) {

                $dto = $container->get($parameterClass);
                $errors = self::validateDTO($dto, $args);

                if (! empty($errors)) {
                    return HTTPResponse::error($errors, 400);
                }
                $properties = (new ReflectionObject($dto))->getProperties();

                foreach($properties as $property) {
                    $propertyName = $property->getName();
                    $dto->$propertyName = $args[$propertyName];
                }
                $newParams[$parameter->getName()] = $dto;
            } else {
                $newParams[$parameter->getName()] = $args[$parameter->getName()];
            }
        }
        return HTTPResponse::success(call_user_func_array([$class, $method], $newParams));
    }

    private static function validateDTO($dto, $args)
    {
        $tokens = array_values(array_filter(
            token_get_all(file_get_contents((new ReflectionObject($dto))->getFileName())),
            function(&$token){
                return (($token[0] == T_COMMENT) && ($token = strstr($token[1], "#Rules("))) || ($token[0] == T_VARIABLE);
            }
        ));

        $errors = [];
        $tokenNum = count($tokens);
        for ($i=0; $i < $tokenNum; $i++) { 
            if (($i + 1) < $tokenNum && $tokens[$i][0] == T_COMMENT && self::string_contains($tokens[$i][1], "#Rules(") && $tokens[($i + 1)][0] == T_VARIABLE) {

                $propertyName = ltrim($tokens[($i + 1)][1], "$");
                $rules = explode(",", substr($tokens[$i][1], 7, strpos($tokens[$i][1], ")") - 7));

                foreach($rules as $rule) {
                    $rule = trim($rule);

                    if (($rule) == "required") {
                        if (! array_key_exists($propertyName, $args)) {
                            $errors[] = "'".$propertyName."' is required";
                        }
                    }

                    if ($rule == "numeric") {
                        if (array_key_exists($propertyName, $args) && ! is_numeric($args[$propertyName])) {
                            $errors[] = "'".$propertyName."' should have a numeric value";
                        }
                    }

                    if ($rule == "text") {
                        if (array_key_exists($propertyName, $args) && ! is_string($args[$propertyName])) {
                            $errors[] = "'".$propertyName."' should have a text value";
                        }
                    }
                }
            }
        }
        return $errors;
    }

    private static function string_contains(string $haystack, string $needle): bool
    {
        if (!function_exists('str_contains')) {
            if (is_string($haystack) && is_string($needle) ) {
                return '' === $needle || false !== strpos($haystack, $needle);
            } else {
                return false;
            }
        }
        return str_contains($haystack, $needle);
    }
}