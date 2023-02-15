<?php

namespace App\Setup;

use ReflectionClass;
use ReflectionParameter;
use ReflectionNamedType;
use ReflectionUnionType;
use Psr\Container\ContainerInterface;
use App\Exceptions\ContainerException;

class Container implements ContainerInterface
{
    private $entries = [];

    public function get($id)
    {
        if($this->has($id)) {

            $entry = $this->entries[$id];

            if(is_callable($entry)) {
                return call_user_func($entry, $this);
            }

            $id = $entry;
        }

        return $this->resolve($id);        
    }

    public function has($id) 
    {
        return isset($this->entries[$id]);
    }

    public function set(string $id, $concrete)
    {
        $this->entries[$id] = $concrete;
    }

    public function resolve(string $id)
    {
        $reflectionClass = new ReflectionClass($id);

        if (! $reflectionClass->isInstantiable()) {
            throw new ContainerException("Class '".$id."' is not instantiable.");
        }

        $constructor = $reflectionClass->getConstructor();

        if (! $constructor) {
            return new $id;
        }

        $parameters = $constructor->getParameters();

        if (! $parameters) {
            return new $id;
        }

        $dependancies = array_map(
            function(ReflectionParameter $param) use ($id) {

                $name = $param->getName();
                $type = $param->getType();

                if (! $type) {
                    throw new ContainerException("Faild to resolve class '".$id."' because parameter '".$name."' is missing a type hint.");
                }

                if ($type instanceof ReflectionUnionType) {
                    throw new ContainerException("Faild to resolve class '".$id."' because of union type for parameter '".$name."'");
                }

                if ($type instanceof ReflectionNamedType && ! $type->isBuiltin()) {
                    return $this->get($type->getName());
                }

                throw new ContainerException("Faild to resolve class '".$id."' because of invalid parameter '".$name."'");
            }, 
            $parameters
        );

        return $reflectionClass->newInstanceArgs($dependancies);
    }
}