<?php

namespace SR\GeoDataTest;

use PDO;

class Coordinates
{
    /**
     * @var PDO
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    public function __construct(PDO $connection, string $tableName)
    {
        $this->connection = $connection;
        $this->tableName = $tableName;
    }
}