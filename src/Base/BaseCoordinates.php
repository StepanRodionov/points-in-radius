<?php

namespace SR\GeoDataTest\Base;

use PDO;

class BaseCoordinates
{
    const DEGREE_LENGTH_IN_METERS = 111153;

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
        $meridianLengthInDegrees = $radiusInMeters / self::DEGREE_LENGTH_IN_METERS;
        $parallelLengthInDegrees = $radiusInMeters / (self::DEGREE_LENGTH_IN_METERS * $this->getParallelMultiplier($lat));
        $upperPart = $lat + $radiusInDegrees;
        $lowerPart = $lat - $radiusInDegrees;
        $leftPart = $lon - $parallelLengthInDegrees;
        $rightPart = $lon + $parallelLengthInDegrees;

        return <<<WHERE
        ($latColumn < $upperPart AND $latColumn > $lowerPart) AND
        ($lonColumn < $rightPart AND $lonColumn > $leftPart)
WHERE;

    }

    private function getParallelMultiplier(float $lat): float
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