<?php

namespace App\Utils;

abstract class HTTPResponse 
{
    public static function error($error, $status = 500) 
    {
        return json_encode([
            "success" => false,
            "status" => $status,
            "error" => $error
        ]);
    }

    public static function success($data, $status = 200) 
    {
        return json_encode([
            "success" => true,
            "status" => $status,
            "data" => $data
        ]);
    }
}