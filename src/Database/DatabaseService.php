<?php

namespace App\Database;

use PDO;

abstract class DatabaseService 
{
    private $type;
    private $host;
    private $port;
    private $name;
    private $username;
    private $password;

    protected function connect()
    {
        $dsn = "mysql:host=".$this->host.";dbname=".$this->name;
        $pdo = new PDO($dsn, $this->username, $this->password);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    }
}
