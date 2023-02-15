<?php

namespace App\Database;

use ReflectionClass;
use ReflectionProperty;

abstract class Entity
{
    public function getTokens()
    {
        $class = new ReflectionClass($this);
        return print_r(token_get_all(file_get_contents($class->getFileName())));
    }

    public function save() {
        // $class = new ReflectionClass($this);
        // $tableName = strtolower($class->getShortName());

        // $propsToImplode = [];

        // foreach ($class->getProperties(ReflectionProperty::IS_PUBLIC) as $property) { // consider only public properties of the providen 
        //     $propertyName = $property->getName();
        //     $propsToImplode[] = '`'.$propertyName.'` = "'.$this->{$propertyName}.'"';
        // }

        // $setClause = implode(',',$propsToImplode); // glue all key value pairs together
        // $sqlQuery = '';

        // if ($this->id > 0) {
        //     $sqlQuery = 'UPDATE `'.$tableName.'` SET '.$setClause.' WHERE id = '.$this->id;
        // } else {
        //     $sqlQuery = 'INSERT INTO `'.$tableName.'` SET '.$setClause.', id = '.$this->id;
        // }

        // $result = self::$db->exec($sqlQuery);

        // if (self::$db->errorCode()) {
        //     throw new \Exception(self::$db->errorInfo()[2]);
        // }

        // return $result;
    }
}