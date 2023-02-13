<?php

namespace App\Config;

use RuntimeException;
use InvalidArgumentException;

abstract class ConfigService 
{

    public static function load(string $path)
    {
        if(!file_exists($path)) {
            throw new InvalidArgumentException(sprintf('%s does not exist', $path));
        }

        if (!is_readable($path)) {
            throw new RuntimeException(sprintf('%s file is not readable', $path));
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (self::isWrappedByChar($value, "\"") || self::isWrappedByChar($value, "'")) {
                $value = substr($value, 1, -1);
            }

            if (in_array(strtolower($value), ["true", "false"])) {
                $value = strtolower($value) === "true"? true : false;
            }

            if (in_array($value, ["null", "Null"], true)) {
                $value = null;
            }

            if(is_numeric($value)) {

                $value = (float) $value;
                $int = filter_var($value, FILTER_VALIDATE_INT);

                if ($int !== false) {
                    $value =  $int;
                }
            }

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }

    private static function isWrappedByChar(string $value, string $char) : bool
    {
        return !empty($value) && $value[0] === $char && $value[-1] === $char;
    }
    
}

?>