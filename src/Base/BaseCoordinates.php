<?php

namespace SR\GeoDataTest\Base;

use PDO;

class BaseCoordinates
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * BaseCoordinates constructor.
     *
     *
     * @param PDO $connection
     * @param string $tableName
     */
    public function __construct(PDO $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName = trim($tableName);
    }

    /**
     * @return array
     */
    public function getAllPoints()
    {
        $sql = "SELECT id, lat, lon from {$this->tableName} order by id";
        return $this->query($sql);
    }

    public function getPoints()
    {

    }

    /**
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param $sql
     * @param null $fetchStyle
     * @return array
     *
     * public because it is test :)
     */
    public function query($sql, $fetchStyle = null)
    {
        $stt = $this->connection->query($sql);
        return $stt->fetchAll($fetchStyle);
    }
}