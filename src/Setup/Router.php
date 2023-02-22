<?php

namespace App\Setup;

use Exception;
use ReflectionObject;

use App\Utils\Helper;
use App\Utils\HTTPRequest;
use App\Utils\HTTPResponse;

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

    public static function put(string $route, $action)
    {
        self::$routes["put"][$route] = $action;
    }

    public static function delete(string $route, $action)
    {
        self::$routes["delete"][$route] = $action;
    }

    public static function resolve(Container $container, string $requestMethod, string $requestUri)
    {
        $route = explode('?', $requestUri)[0];
        $action = self::$routes[$requestMethod][$route]?? null;

        if (! $action) {
            return HTTPResponse::error(["Route '".$route."' not found"], 404);
        }

        if (is_array($action)) {
            $class = $action[0];
            $method = $action[1];

            if (class_exists($class)) {
                $class = $container->get($class);

                if (method_exists($class, $method)) {

                    return self::validateParamsAndCall($container, $class, $method, HTTPRequest::getInputs());
                }
                throw new Exception("Method '".$method."' does not exists inside class '".$class."'");
            }
            throw new Exception("Class '".$class."' does not exists");
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
                    if (array_key_exists($propertyName, $args) && ! empty($args[$propertyName])) {
                        $dto->$propertyName = $args[$propertyName];
                    } else {
                        unset($dto->$propertyName);
                    }
                }
                $newParams[$parameter->getName()] = $dto;
            } 
            else {
                $parameterName = $parameter->getName();
                if (!array_key_exists($parameterName, $args)) {
                    return HTTPResponse::error(["'".$parameterName."' is required"], 400);
                }

                try {
                    $args[$parameterName] = Helper::convert($parameterClass, $args[$parameterName]);
                } catch (Exception $e) {
                    return HTTPResponse::error([$e->getMessage()], 400);
                }

                $newParams[$parameterName] = $args[$parameterName];
            }
        }
        return HTTPResponse::success(call_user_func_array([$class, $method], $newParams));
    }

    private static function validateDTO($dto, $args)
    {
        $tokens = array_values(array_filter(
            token_get_all(file_get_contents((new ReflectionObject($dto))->getFileName())),
            function(&$token){
                return (($token[0] == T_COMMENT) && ($token = strstr($token[1], "#Rules["))) || ($token[0] == T_VARIABLE);
            }
        ));

        $errors = [];
        $tokenNum = count($tokens);
        for ($i=0; $i < $tokenNum; $i++) { 
            if (($i + 1) < $tokenNum && $tokens[$i][0] == T_COMMENT && Helper::string_starts_with($tokens[$i][1], "#Rules[") && $tokens[($i + 1)][0] == T_VARIABLE) {

                $propertyName = ltrim($tokens[($i + 1)][1], "$");
                $rules = explode(",", substr($tokens[$i][1], 7, strpos($tokens[$i][1], "]") - 7));

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

                    if ($rule == "text" || $rule == "string") {
                        if (array_key_exists($propertyName, $args) && ! is_string($args[$propertyName])) {
                            $errors[] = "'".$propertyName."' should have a text value";
                        }
                    }

                    if (Helper::string_starts_with($rule, "required_when")) {
                        $conditions = substr($rule, 14, strpos($rule, ")") - 14);
                        $attribute = explode("=", $conditions);

                        if ($args[$attribute[0]] == $attribute[1]) {
                            if (! array_key_exists($propertyName, $args)) {
                                $errors[] = "'".$propertyName."' is required";
                            }
                        }
                    }

                    if (Helper::string_starts_with($rule, "unique")) {
                        $className = substr($rule, 7, strpos($rule, ")") - 7);
                        $repository = Database::getRepository($className);

                        if (! empty($repository->getWhere([$propertyName => $args[$propertyName]], "id"))) {
                            $errors[] = "'".$propertyName."' already exists";
                        }
                    }

                }
            }
        }
        return $errors;
    }
}