<?php

namespace App\Utils;

class HTTPRequest
{
    public static function getInputs()
    {
        $method = $_SERVER['REQUEST_METHOD'];
    
        switch ($method)
        {
            case "GET":
                return $_GET;

            case "POST":
                return $_POST;

            case 'PUT':
            case "DELETE":
                    return self::parseInput();
        
            default:
                return null;
        }
    }

    private static function parseInput()
    {
        $data = file_get_contents("php://input");

        if($data == false)
            return array();

        parse_str($data, $result);

        return $result;
    }
}