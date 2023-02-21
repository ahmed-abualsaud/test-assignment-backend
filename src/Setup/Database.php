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

    public static function getRepository(string $class)
    {
        $metadata = DataSource::parseEntity($class);
        return new Database($metadata["table"], $metadata["columns"]);
    }

    public function all(...$select)
    {
        try {
            $columns = array_filter(array_keys($this->columns), function($column) { 
                return (! in_array("hidden", $this->columns[$column]));
            });

            $diff = array_values(array_diff($select, $columns));
            if (! empty($diff)) {
                throw new PDOException("Unknown columns '".$diff[0]."'");
            }
            $selectColumns = empty($select)? "*": implode(", ", $select);
            $query = "SELECT ".$selectColumns." FROM ".$this->table;
            $connection = $this->dataSource->openConnection();
            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$query." ".$e->getMessage());
        }
    }

    public function getWhere($andCriteria, ...$select)
    {
        try {
            $columns = array_filter(array_keys($this->columns), function($column) { 
                return (! in_array("hidden", $this->columns[$column]));
            });

            $diff = array_values(array_diff($select, $columns));
            if (! empty($diff)) {
                throw new PDOException("Unknown columns '".$diff[0]."'");
            }

            $selectColumns = empty($select)? "*": implode(", ", $select);
            $query = "SELECT ".$selectColumns." FROM ".$this->table." WHERE ".$this->andWhere($andCriteria);
            $connection = $this->dataSource->openConnection();
            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$query." ".$e->getMessage());
        }
    }

    public function getWhereInnerJoin($joins, $andCriteria, ...$select)
    {
        try {
            $columns = array_filter(array_keys($this->columns), function($column) { 
                return (! in_array("hidden", $this->columns[$column]));
            });

            $diff = array_values(array_diff($select, $columns));
            if (! empty($diff)) {
                throw new PDOException("Unknown columns '".$diff[0]."'");
            }

            $selectColumns = empty($select)? "*": implode(", ", $select);
            $query = "SELECT ".$selectColumns." FROM ".$this->table." ";
            foreach ($joins as $join) {
                $query .= "INNER JOIN ".$join[0]." ON ".$this->table.".".$join[2]."=".$join[0].".".$join[1]." ";
            }

            if (! empty($andCriteria)) {
                $query .= "WHERE ".$this->andWhere($andCriteria);
            }
            $connection = $this->dataSource->openConnection();
            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$query." ".$e->getMessage());
        }
    }

    public function executeReadQuery($query)
    {   
        try {
            $connection = $this->dataSource->openConnection();
            $statement = $connection->prepare($query);
            $statement->execute();
            $result = $statement->fetchAll();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$e->getMessage());
        }
    }

    public function create($args)
    {
        try {
            $columns = array_filter(array_keys($this->columns), function($column) { 
                return (! in_array("id", $this->columns[$column]));
            });

            foreach ($columns as $column) {
                if (! array_key_exists($column, $args) && ! in_array("nullable", $this->columns[$column])) {
                    throw new PDOException("'".$column."' is required");
                }
            }

            $argsKeys = array_keys($args);
            $diff = array_values(array_diff($argsKeys, $columns));
            if (! empty($diff)) {
                throw new PDOException("Unknown columns '".$diff[0]."'");
            }

            $columns = $argsKeys;
            $columnsString = implode(", ", $columns);
            $columnsParams = ":".implode(", :", $columns);
            $query = "INSERT INTO ".$this->table." (".$columnsString.") VALUES (".$columnsParams.")";
            $connection = $this->dataSource->openConnection();
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
            return $result[0];

        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$query." ".$e->getMessage());
        }
    }

    public function insert($arguments)
    {
        $query= "";
        try {
            $allColumns = array_filter(array_keys($this->columns), function($column) { 
                return (! in_array("id", $this->columns[$column]));
            });

            $results = [];
            $connection = $this->dataSource->openConnection();
            $connection->beginTransaction();

            foreach($arguments as $args) {
                foreach ($allColumns as $column) {
                    if (! array_key_exists($column, $args) && ! in_array("nullable", $this->columns[$column])) {
                        throw new PDOException("'".$column."' is required");
                    }
                }
    
                $argsKeys = array_keys($args);
                $diff = array_values(array_diff($argsKeys, $allColumns));
                if (! empty($diff)) {
                    throw new PDOException("Unknown columns '".$diff[0]."'");
                }

                $columns = $argsKeys;
                $columnsString = implode(", ", $columns);
                $columnsParams = ":".implode(", :", $columns);
                $query = "INSERT INTO ".$this->table." (".$columnsString.") VALUES (".$columnsParams.")";
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
                $results[] = $statement->fetchAll()[0];
            }

            $connection->commit();
            $this->dataSource->closeConnection();
            return $results;

        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$query." ".$e->getMessage());
        }
    }

    public function delete($criteria)
    {
        try {
            $query = "DELETE FROM ".$this->table." WHERE ".$this->andWhere($criteria);
            $connection = $this->dataSource->openConnection();
            $statement = $connection->prepare($query);
            $result = $statement->execute();
            $this->dataSource->closeConnection();
            return $result;
        } catch (PDOException $e) {
            throw new PDOException("Execute query failed: ".$query." ".$e->getMessage());
        }
    }

    function andWhere($andCriteria)
    {
        $anded = "";

        foreach ($andCriteria as $key => $value) {
            if (gettype($value) === "string" && Helper::string_starts_with($value, "IN(")) {

                $anded .= $key." ".$value." AND ";
            } else {
                $anded .= $key."='".$value."' AND ";
            }
        }
        return substr($anded, 0, strlen($anded) - 5);
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