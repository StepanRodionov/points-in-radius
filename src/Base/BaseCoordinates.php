<?php

namespace SR\GeoDataTest\Base;

use PDO;

class BaseCoordinates
{
    const DEGREE_LENGTH_IN_METERS = 111153;

    /**
     * @var PDO
     */
    protected $connection;

    /**
     * @var string
     */
    protected $tableName;

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
     * @return array|null
     */
    public function getAllPoints(): ?array
    {
        $sql = "SELECT id, lat, lon from {$this->tableName} order by id";
        return $this->query($sql);
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @return array|null
     */
    public function getPoints($lat, $lon, $radiusInMeters): ?array
    {
        $sql = <<<SQL
    SELECT id, lat, lon {$this->getCustomSelect($lat, $lon, $radiusInMeters)} from {$this->tableName} 
    where {$this->getSqlSquareWhere($lat, $lon, $radiusInMeters)} AND {$this->getCustomWhere($lat, $lon, $radiusInMeters)} 
    order by id
SQL;
        //  echo $sql; die();       //  Uncomment to get SQL query in your browser
        return $this->query($sql);
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param string $latColumn
     * @param string $lonColumn
     * @return string
     *
     * Keeps class-specific logic of select
     */
    public function getCustomWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        /** TODO - implement in subclasses */
        return '1 = 1';
    }


    /**
     * @return string
     *
     * Keeps specific select for queries
     */
    public function getCustomSelect($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        /** TODO - implement in subclasses */
        return '';
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param string $latColumn
     * @param string $lonColumn
     * @return string
     *
     * return value should be used as-is in SELECT statement
     */
    public function getSqlSquareWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        //return '1 = 1';       // uncomment to remove square where
        $meridianLengthInDegrees = $radiusInMeters / self::DEGREE_LENGTH_IN_METERS;
        $parallelLengthInDegrees = $radiusInMeters / (self::DEGREE_LENGTH_IN_METERS * $this->getParallelMultiplier($lat));
        $upperPart = $lat + $meridianLengthInDegrees;
        $lowerPart = $lat - $meridianLengthInDegrees;
        $leftPart = $lon - $parallelLengthInDegrees;
        $rightPart = $lon + $parallelLengthInDegrees;

        return <<<WHERE
        ($latColumn < $upperPart AND $latColumn > $lowerPart) AND
        ($lonColumn < $rightPart AND $lonColumn > $leftPart)
WHERE;

    }

    /**
     * @param float $lat
     * @return float
     */
    protected function getParallelMultiplier(float $lat): float
    {
        return cos(deg2rad($lat));
    }

    /**
     * @return PDO
     */
    public function getConnection(): PDO
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