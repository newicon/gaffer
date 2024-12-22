<?php declare(strict_types=1);

namespace Gaffer\Db;

use PDO;

/**
 * basic pdo wrapper
 */
class DB
{
    /** @var PDO $pdo */
    private PDO $pdo;

    /** @var self */
    private static DB $instance;

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    private function __construct(string $dsn, string $username, string $password)
    {
        $this->pdo = new PDO($dsn, $username, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    /**
     * @param string $dsn
     * @param string $username
     * @param string $password
     */
    public static function init(string $dsn, string $username, string $password): void
    {
        self::$instance = new self($dsn, $username, $password);
    }

    /**
     * @param string $sql
     * @param null|array $params
     * @return \PDOStatement
     * @throws \Exception
     */
    private static function executeQuery(string $sql,array $params=null): \PDOStatement
    {
        $matches=[];
        preg_match_all("/(:[^\s),]*)\s?/", $sql, $matches);
        $sqlPdoParams = $matches[1];
        if (count($sqlPdoParams)) {
            if (!$params) {
                throw new \Exception("PDO params were specified in '".$sql."' but none were passed!");
            }
            if (count($sqlPdoParams) != count($params)) {
                throw new \Exception("The PDO params in '".$sql."' do not match the passed params " .
                    "'".implode("', '", array_keys($params))."'");
            }
            foreach ($sqlPdoParams as $checkParam) {
                $paramName = substr($checkParam,1);
                if (!array_key_exists($paramName, $params)) {
                    throw new \Exception("The PDO param '".$paramName."'" .
                        " was specified in the SQL but not included in the passed params");
                }
            }
        }

        if (is_array($params)) {
            // handle an array of parameters (useful for 'IN' clauses etc)
            $updatedParams = $params;
            $updatedSql = $sql;
            foreach ($params as $key=>$value) {
                if (is_array($value)) {
                    $replaceInSql=[];
                    $idx=1;
                    foreach ($value as $valueItem) {
                        $newKey = $key.$idx;
                        $replaceInSql[] = ":".$newKey;
                        $updatedParams[$newKey] = $valueItem;
                        $idx++;
                    }
                    $updatedSql = str_replace(":".$key, implode(",",$replaceInSql), $updatedSql);
                    unset($updatedParams[$key]);
                }
            }
            $sql = $updatedSql;
            $params = $updatedParams;
        }

        $pdoStatement  = self::$instance->pdo->prepare($sql);
        if (!$pdoStatement) {
            throw new \Exception("Failed to prepare (".$pdoStatement->errorCode().") : "
                .print_r($pdoStatement->errorInfo(),true)
                .$sql." : ".print_r($params,true));
        }
        if (!$pdoStatement->execute($params)) {
            throw new \Exception("Failed to execute (".$pdoStatement->errorCode().") : "
                .print_r($pdoStatement->errorInfo(),true)
                .$sql." : ".print_r($params,true));
        }
        return $pdoStatement;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return array|false
     * @throws \Exception
     */
    public static function all(string $sql, array $params=null): bool|array
    {
        return self::executeQuery($sql, $params)
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return array|null
     * @throws \Exception
     */
    public static function one(string $sql, array $params=null): ?array
    {
        $result = self::executeQuery($sql, $params)
            ->fetch(PDO::FETCH_ASSOC);
        return is_array($result) ? $result : null;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return array|false
     * @throws \Exception
     */
    public static function column(string $sql, array $params=null): bool|array
    {
        return self::executeQuery($sql, $params)
            ->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @return mixed|null
     * @throws \Exception
     */
    public static function scalar(string $sql, array $params=null): mixed
    {
        $columnValues = self::column($sql, $params);
        return $columnValues[0] ?? null;
    }

    /**
     * @param string $table
     * @param array $inserts
     * @return int the id of the inserted row
     * @throws \Exception
     */
    public static function insert(string $table, array $inserts): int
    {
        if (count($inserts)===0) {
            return 0;
        }
        $sql = "INSERT INTO `".$table."` (`".implode("`, `",array_keys($inserts))."`)"
            ." VALUES (:".implode(", :",array_keys($inserts)).")";
        $pdoStatement = self::executeQuery($sql, $inserts);
        $rowCount = $pdoStatement->rowCount();
        if ($rowCount!==1) {
            throw new \Exception("Unexpected row count after insert : ".$rowCount);
        }
        else {
            return (int)self::$instance->pdo->lastInsertId();
        }
    }

    /**
     * @param string $table
     * @param array $updates
     * @param string|null $where
     * @param array|null $whereParams
     * @return int number of affected rows
     * @throws \Exception
     */
    public static function update(string $table, array $updates, string $where=null, array $whereParams=null): int
    {
        $setParts = [];
        foreach (array_keys($updates) as $updateKey) {
            $setParts[] = "`".$updateKey."`=:".$updateKey;
        }
        $allParams = $updates;
        $sql = "UPDATE `".$table."` SET ".implode(", ", $setParts);
        if (is_string($where)) {
            $sql .=" WHERE ".$where;
            if (is_array($whereParams) && count($whereParams)>0) {
                $allParams = array_merge($allParams, $whereParams);
            }
        }
        $pdoStatement = self::executeQuery($sql, $allParams);
        return $pdoStatement->rowCount();
    }

    /**
     * @param string $table
     * @param string $where
     * @param array|null $whereParams
     * @return int number of affected rows
     * @throws \Exception
     */
    public static function delete(string $table, string $where, array $whereParams=null): int
    {
        $sql = "DELETE FROM `".$table."` WHERE ".$where;
        $pdoStatement = self::executeQuery($sql, $whereParams);
        return $pdoStatement->rowCount();
    }
}
