<?php

namespace App\Utils;

use App\Utils\HTTPMessage;

abstract class HTTPResponse 
{
    public static function error($error, $status = 500) 
    {
        return json_encode([
            "success" => false,
            "status" => $status,
            "message" => HTTPMessage::getMessage($status),
            "error" => $error? $error: null
        ]);
    }

    public static function success($data, $status = 200) 
    {
        return json_encode([
            "success" => true,
            "status" => $status,
            "message" => HTTPMessage::getMessage($status),
            "data" => $data? $data: null
        ]);
    }
}