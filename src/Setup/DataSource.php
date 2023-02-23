<?php

namespace App\Setup;

use PDO;
use PDOException;
use ReflectionClass;
use App\Utils\Helper;

class DataSource
{
    private $type;
    private $host;
    private $port;
    private $name;
    private $username;
    private $password;
    private $connection;


    public function __construct()
    {
        $this->type = Config::get("DATABASE_TYPE");
        $this->host = Config::get("DATABASE_HOST");
        $this->port = Config::get("DATABASE_PORT");
        $this->name = Config::get("DATABASE_NAME");
        $this->username = Config::get("DATABASE_USERNAME");
        $this->password = Config::get("DATABASE_PASSWPRD");
    }

    protected function connect()
    {
        try{
            $dsn = $this->type.":host=".$this->host.";dbname=".$this->name;
            $pdo = new PDO($dsn, $this->username, $this->password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->connection = $pdo;
            return $this;
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: ".$e->getMessage());
        }
    }

    public function openConnection()
    {
        return $this->connection;
    }

    public function closeConnection()
    {
        $this->connection = null;
    }

    public static function getSource()
    {
        return (new DataSource())->connect();
    }


    public static function parseEntity($class)
    {
        $class = new ReflectionClass($class);
        $tokens = token_get_all(file_get_contents($class->getFileName()));
        $tokensCount = count($tokens);
        $filterdTokens = [];

        foreach ($tokens as $key => $token) {
            if (($token[0] == T_COMMENT) && Helper::string_starts_with($token[1], "#Column[") || ($token[0] == T_VARIABLE)) {
                $filterdTokens[] = $token;
            }

            if ($token[0] == T_CLASS) {
                for ($i=$key - 1; $i >= 0; $i--) { 
                    if (($tokens[$i][0] == T_COMMENT) && 
                        (Helper::string_starts_with($tokens[$i][1], "#Entity(") || Helper::string_starts_with($tokens[$i][1], "#Table("))) {
                        $filterdTokens[] = $tokens[$i];
                        break;
                    }
                }

                for ($j=$key + 1; $j < $tokensCount; $j++) { 
                    if ($tokens[$j][0] == 319) {
                        $filterdTokens[] = $tokens[$j];
                        break;
                    }
                }
            }
        }

        $tokenNum = count($filterdTokens);
        $result = [];
        for ($i=0; $i < $tokenNum; $i++) {
            if ($filterdTokens[$i][0] == T_COMMENT) {

                if (Helper::string_starts_with($filterdTokens[$i][1], "#Entity(")) {
                    $result["table"] = trim(substr($filterdTokens[$i][1], 8, strpos($filterdTokens[$i][1], ")") - 8));
                }

                if (Helper::string_starts_with($filterdTokens[$i][1], "#Table(")) {
                    $result["table"] = trim(substr($filterdTokens[$i][1], 7, strpos($filterdTokens[$i][1], ")") - 7));
                }

                if (Helper::string_starts_with($filterdTokens[$i][1], "#Column[")) {
                    if (($i + 1) < $tokenNum && $filterdTokens[($i + 1)][0] == T_VARIABLE) {

                        $columnName = ltrim($filterdTokens[($i + 1)][1], "$");
                        $rules = explode(",", substr($filterdTokens[$i][1], 8, strpos($filterdTokens[$i][1], "]") - 8));
                        $rules = array_map(function($rule) {
                            return trim($rule);
                        }, $rules);
                        $result["columns"][$columnName] = $rules; 
                    }
                }
            }
        }

        if (! array_key_exists("table", $result)) {
            $tableName = Helper::decamelize($class->getShortName());

            if ($tableName[strlen($tableName) - 1] === 'y' && ! in_array($tableName[strlen($tableName) - 2], ['a', 'e', 'i', 'o', 'u'])) {
                $tableName = substr($tableName, 0, strlen($tableName) - 1)."ies";
            } else {
                $tableName .= 's';
            }

            $result["table"] = $tableName;
        }

        return $result;
    }
}
