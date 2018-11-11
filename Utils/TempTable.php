<?php

namespace AlexJumperman\TempTableBundle\Utils;


use Doctrine\DBAL\Connection;

class TempTable
{
    private $connection;
    private $tableName;
    private $sql;
    private $isCreated = false;

    public function __construct(Connection $connection, $tableName, $sql)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->sql = $sql;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function create()
    {
        if(!$this->isCreated) {
            $this->connection->query('create temporary table ' . $this->tableName . ' as ( ' . $this->sql . ')');
            $this->isCreated = true;
        }
    }
}