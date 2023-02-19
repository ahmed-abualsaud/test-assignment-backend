<?php

namespace App\Setup;

use PDOException;
use App\Utils\Helper;

class Database
{

    private $table;
    private $columns;
    private $dataSource;

    public function __construct($table, $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
        $this->dataSource = DataSource::getSource();
    }

    public static function getRepository($class)
    {
        $metadata = DataSource::parseEntity($class);
        return new Database($metadata["table"], $metadata["columns"]);
    }

    public function all()
    {
        try {
            $connection = $this->dataSource->openConnection();
            $columnsString = implode(", ", array_keys($this->columns));
            $statement = $connection->prepare("SELECT ".$columnsString." FROM ".$this->table);
            $statement->execute();
            $result = $statement->fetchAll();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: ".$e->getMessage());
        }
    }

    public function create($args)
    {
        try {
            $connection = $this->dataSource->openConnection();
            $columns = array_filter(array_keys($this->columns), function($column) { 
                return (! in_array("id", $this->columns[$column]));
            });

            foreach ($columns as $column) {
                if (! array_key_exists($column, $args)) {
                    throw new PDOException("'".$column."' is required");
                }
            }

            $diff = array_values(array_diff(array_keys($args), $columns));
            if (! empty($diff)) {
                throw new PDOException("Unknown columns '".$diff[0]."'");
            }

            $columnsString = implode(", ", $columns);
            $columnsParams = ":".implode(", :", $columns);
            $query = "INSERT INTO ".$this->table." (".$columnsString.") VALUES (".$columnsParams.")";
            $connection->beginTransaction();
            $statement = $connection->prepare($query);

            foreach ($columns as $column) {
                $statement->bindParam(":".$column, $args[$column]);
            }

            $statement->execute();
            $result = $connection->lastInsertId();

            $primary = array_filter(array_keys($this->columns), function($column) { 
                return in_array("id", $this->columns[$column]);
            });

            $query = "SELECT * FROM ".$this->table." WHERE ".$primary[0]."=".$result;
            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $connection->commit();
            $this->dataSource->closeConnection();
            return $result;

        } catch (PDOException $e) {
            //$connection->rollback();
            throw new PDOException("Connection failed: ".$e->getMessage());
        }
    }

    public function delete($ids)
    {
        try {
            $connection = $this->dataSource->openConnection();
            $query = "DELETE FROM ".$this->table." WHERE id IN(".trim($ids, "[]").")";
            $statement = $connection->prepare($query);
            $result = $statement->execute();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: ".$e->getMessage());
        }
    }

    private function parseType($value, $rules)
    {
        foreach ($rules as $rule) {
            if ($rule === "id") {
                return Helper::convert("int", $value);
            }

            if ($rule === "numeric") {
                return Helper::convert("float", $value);
            }

            if ($rule === "bool") {
                return Helper::convert("bool", $value);
            }

            if ($rule === "string") {
                return Helper::convert("string", $value);
            }
        }
    }
}