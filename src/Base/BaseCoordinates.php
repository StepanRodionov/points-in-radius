<?php

namespace SR\Base\GeoDataTest;

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
        $dbRes = $this->connection->query($sql);
        $data = $dbRes->fetchAll();
        return $data;
    }

    abstract function getPoints();
}