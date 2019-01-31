<?php


namespace SR\GeoDataTest;


use PDO;
use SR\GeoDataTest\Base\BaseCoordinates;

class HaversineCoordinates extends BaseCoordinates
{
    const EARTH_RADIUS_IN_METERS = 6371000;

    const PROCEDURE_NAME = 'distance_between';

    private static $TYPES = [
        'simple' => 'getSimpleWhere',
        'proc' => 'getProcedure',
        'opt' => 'getOprimizedWhere',
        'more_opt' => 'getMoreOptimizedWhere',
    ];

    private $typeWhere;

    public function __construct(PDO $connection, $tableName, $type = null)
    {
        parent::__construct($connection, $tableName);
        if (!$type || !isset(self::$TYPES[$type])) {
            $type = 'simple';
        }
        $this->typeWhere = self::$TYPES[$type];
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param string $latColumn
     * @param string $lonColumn
     * @return string
     */
    public function getCustomWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        return $this->{$this->typeWhere}($lat, $lon, $radiusInMeters, $latColumn, $lonColumn);

    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param $latColumn
     * @param $lonColumn
     * @return string
     */
    private function getProcedure($lat, $lon, $radiusInMeters, $latColumn, $lonColumn)
    {
        $procName = self::PROCEDURE_NAME;
        return <<<WHERE
        {$procName}({$latColumn}, {$lonColumn}, {$lat}, $lon) < {$radiusInMeters}
WHERE;
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param $latColumn
     * @param $lonColumn
     * @return string
     */
    private function getSimpleWhere($lat, $lon, $radiusInMeters, $latColumn, $lonColumn)
    {
        $radius = self::EARTH_RADIUS_IN_METERS;
        return <<<WHERE
        {$radius} * 2 * ASIN(
        SQRT(
            POWER(
              SIN(($latColumn - ABS($lat)) * PI() / 180 / 2),
              2
            ) +
            COS($latColumn * PI() / 180) *
            COS(ABS($lat) * PI() / 180) *
            POWER(
              SIN(($lonColumn - $lon) * PI() / 180 / 2),
              2
            )
        )
      ) < {$radiusInMeters}
WHERE;
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param $latColumn
     * @param $lonColumn
     * @return string
     */
    private function getOprimizedWhere($lat, $lon, $radiusInMeters, $latColumn, $lonColumn)
    {
        $radius = self::EARTH_RADIUS_IN_METERS;
        $piBy180 = pi() / 180;
        return <<<WHERE
        {$radius} * 2 * ASIN(
        SQRT(
            POWER(
              SIN(($latColumn - ABS($lat)) * {$piBy180} / 2),
              2
            ) +
            lat_cos *
            COS(ABS($lat) * {$piBy180}) *
            POWER(
              SIN(($lonColumn - $lon) * {$piBy180} / 2),
              2
            )
        )
      ) < {$radiusInMeters}
WHERE;
    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param $latColumn
     * @param $lonColumn
     * @return string
     */
    private function getMoreOptimizedWhere($lat, $lon, $radiusInMeters, $latColumn, $lonColumn)
    {
        $radius = self::EARTH_RADIUS_IN_METERS;
        $piBy180 = pi() / 180;
        $secondCos = cos(deg2rad($lat));
        return <<<WHERE
        {$radius} * 2 * ASIN(
        SQRT(
            POWER(
              SIN(($latColumn - ABS($lat)) * {$piBy180} / 2),
              2
            ) +
            COS($latColumn * PI() / 180) *
            {$secondCos} *
            POWER(
              SIN(($lonColumn - $lon) * {$piBy180} / 2),
              2
            )
        )
      ) < {$radiusInMeters}
WHERE;
    }


}