<?php

namespace CMW\Manager\Database;


use CMW\Manager\ORM\Attributes\Column;
use CMW\Manager\ORM\Attributes\ColumnType;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Actions;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\ColumnWithoutType;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Element;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Sort\Sort;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Sort\SortType;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Table;
use CMW\Manager\ORM\SGBD\Data\Parts\Types\Where\WhereOperator;
use CMW\Manager\ORM\SGBD\Data\SGBDReceiver;
use Exception;
use PDO;
use PDOException;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\ORM\Database\getSGBDInstance;
use CMW\Manager\ORM\Database\SGBD;

class MariaDBDatabase implements SGBD
{

    use getSGBDInstance;

    private function setDatabaseAttributes(PDO $pdo): void
    {
        $pdo->setAttribute(PDO::ATTR_PERSISTENT, true);
        $pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8mb4");
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    private function createDatabase(PDO $pdo): void
    {
        $pdo->exec("SET CHARACTER SET utf8mb4");
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . EnvManager::getInstance()->getValue("DB_NAME") . ";");
        $pdo->exec("USE " . EnvManager::getInstance()->getValue("DB_NAME") . ";");
    }

    public function connect(): PDO
    {
        try {
            $host = EnvManager::getInstance()->getValue("DB_HOST");
            $user = EnvManager::getInstance()->getValue("DB_USERNAME");
            $pass = EnvManager::getInstance()->getValue("DB_PASSWORD");

            $instance = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);

            $this->setDatabaseAttributes($instance);

            $this->createDatabase($instance);

            return $instance;
        } catch (PDOException $e) {
            die("DATABASE ERROR" . $e->getMessage()); //TODO see to implement error :D
        }
    }

    private function printElement(Element $element): string
    {
        if ($element->getAlias() !== null) {
            return $element->getName() . " AS " . $element->getAlias();
        }
        return $element->getName();
    }

    /**
     * @param ColumnWithoutType[]|string[] $columnList
     */
    private function generateColumns(array $columnList): string
    {
        if(count($columnList) === 1 && $columnList[0] === '*') {
            return '*';
        }

        $query = "";
        foreach ($columnList as $column) {
            $query .= $this->printElement($column) . ", ";
        }
        return substr($query, 0, -2);
    }

    private function generateStatement(Actions $action, array $columnList, Table $table, string &$query): void
    {
        switch ($action) {
            case Actions::SELECT:
                $query = "SELECT " . $this->generateColumns($columnList) . " FROM " . $this->printElement($table);
                break;
            case Actions::INSERT:
                //$query = $this->generateInsert($receiver);
                break;
            case Actions::UPDATE:
                //$query = $this->generateUpdate($receiver);
                break;
            case Actions::DELETE:
                //$query = $this->generateDelete($receiver);
                break;
        }
        $query .= " ";
    }

    private function generateWhere(array $whereList, &$query): void
    {
        $roundSingleQuotes = static function ($value) {
            //if value has type of string round by single quotes
            if (is_string($value)) {
                return "'" . $value . "'";
            }
            return $value;
        };
        $countWhere = 0;
        foreach ($whereList as $where) {
            $query .= ($countWhere === 0) ? "WHERE " : "AND ";

            $query .= $where->getColumn() . " ";

            switch ($where->getOperator()) {
                case WhereOperator::IS_NULL:
                    $query .= "IS NULL";
                    break;
                case WhereOperator::IS_NOT_NULL:
                    $query .= "IS NOT NULL";
                    break;
                case WhereOperator::EQUALS:
                    $query .= "= " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::NOT_EQUALS:
                    $query .= "!= " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::GREATER_THAN:
                    ;
                    $query .= "> " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::GREATER_THAN_OR_EQUALS:
                    $query .= ">= " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::LESS_THAN:
                    $query .= "< " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::LESS_THAN_OR_EQUALS:
                    $query .= "<= " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::LIKE:
                    $query .= "LIKE " . $roundSingleQuotes($where->getValue());
                    break;
                case WhereOperator::BETWEEN:
                    $query .= "BETWEEN " . $roundSingleQuotes($where->getValue()[0]) . " AND " . $roundSingleQuotes($where->getValue()[1]);
                    break;
                case WhereOperator::IN:
                    $query .= "IN (" . implode(", ", $roundSingleQuotes($where->getValue())) . ")";
                    break;
            }

            $query .= " ";
            $countWhere++;
        }

    }

    private function generateOrder(array $orderByList, string &$query): void
    {
        $countOrderBy = 0;
        foreach ($orderByList as $orderBy) {
            $columns = implode(", ", $orderBy->getColumns());
            $type = match ($orderBy->getType()) {
                SortType::ASC => "ASC",
                SortType::DESC => "DESC",
            };
            if ($countOrderBy !== 0) {
                $query .= "AND ";
            }

            $query .= "ORDER BY " . $columns . " " . $type . " ";
            $countOrderBy++;
        }
    }

    private function generateLimit(int $limit, string &$query): void
    {
        if ($limit > 0) {
            $query .= "LIMIT " . $limit;
        }
    }

    public function generate(SGBDReceiver $receiver): array
    {
        $query = "";
        $joinTableList = $receiver->getLocation()->getJoinTableList();
        $orderByList = $receiver->getFilter()->getSortList();
        $limit = $receiver->getFilter()->getLimit();

        $this->generateStatement(
            $receiver->getStatement()->getAction(),
            $receiver->getStatement()->getColumns(),
            $receiver->getLocation()->getTable(),
            $query);

        //todo generate join

        $this->generateWhere(
            $receiver->getFilter()->getWhere(),
            $query
        );

        $this->generateOrder(
            $receiver->getFilter()->getSortList(),
            $query
        );

        //todo set limit can have two values
        $this->generateLimit(
            $receiver->getFilter()->getLimit(),
            $query
        );

        //TODO read receiver and generate query :D
        /**
         * @var PDO $pdo
         */
        $pdo = self::getInstance();

        //TODO remove this
        echo "<pre>";
        echo "============ QUERY ============\n";
        echo $query . "\n";
        echo "============ RESULT ============\n";

        $returns = $pdo->prepare($query);
        $res = $returns->execute();

        if ($res === false) {
            throw new Exception("Error in query: " . $query);
        }

        return $returns->fetchAll();
    }

    private function describeType(string $type): ColumnType
    {
        $parenthesisPos = strpos($type, '(');
        if ($parenthesisPos !== false) {
            $type = substr($type, 0, $parenthesisPos);
        }

        return match ($type) {
            'int' => ColumnType::INT,
            'bool' => ColumnType::BOOLEAN,
            'float', 'double' => ColumnType::FLOAT,
            'date', 'datetime', 'timestamp' => ColumnType::DATETIME,
            'blob' => ColumnType::BLOB,
            'json' => ColumnType::JSON,
            default => ColumnType::TEXT,
        };
    }

    public function describe(string $table): array
    {
        $query = "DESCRIBE $table";
        /**
         * @var PDO $pdo
         */
        $pdo = self::getInstance();
        $returns = $pdo->prepare($query);
        $res = $returns->execute();

        if ($res === false) {
            throw new Exception("Error in query: " . $query);
        }

        $columns = array();

        foreach ($returns->fetchAll() as $row) {
            $columns[] = new Column(
                $row["Field"],
                $this->describeType($row["Type"]),
                $row["Null"] === "YES",
            );
        };


        return $columns;
    }
}