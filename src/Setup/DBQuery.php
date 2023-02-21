<?php

namespace App\Setup;

abstract class DBQuery
{
    public static function whereIn($values)
    {
        if (is_array($values)) {
            return "IN(".self::commaConcat($values).")";
        }

        if (is_string($values)) {
            return "IN('".$values."')";
        }

        return "IN(".$values.")";
    }

    private function commaConcat($values)
    {
        $concat = "";
        foreach ($values as $value) {
            if (is_string($value)) {
                $concat .= "'".$value."', ";
            } else {
                $concat .= $value.", ";
            }
        }
        return substr($concat, 0, strlen($concat) - 2);
    }
}