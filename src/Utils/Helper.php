<?php

namespace App\Utils;

abstract class Helper
{
    public static function decamelize($string) {
        return strtolower(preg_replace(['/([a-z\d])([A-Z])/', '/([^_])([A-Z][a-z])/'], '$1_$2', $string));
    }

    public static function string_starts_with(string $haystack, string $needle): bool
    {
        if (!function_exists('str_starts_with')) {
            return (substr($haystack, 0, strlen($needle)) === $needle);
        }
        return str_starts_with($haystack, $needle);
    }

    public static function string_contains(string $haystack, string $needle): bool
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
    
    public static function array_only($array, $keys)
    {
        return array_filter(
            $array, 
            function($key) use ($keys) {
                return in_array($key, $keys);
            }, 
            ARRAY_FILTER_USE_KEY
        );
    }

    public static function convert(string $type, $value)
    {
        switch ($type) {
            case "int":
            case "integer":
                    return (int) $value;

            case "float":
                return (float) $value;
            
            case "double":
                return (double) $value;
            
            case "string":
                return (string) $value;

            case "bool":
                return (bool) $value;
            
            default:
                return $value;
        }
    }
}