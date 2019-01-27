<?php

namespace SR\GeoDataTest;

use \SR\GeoDataTest\Base\BaseCoordinates;

class MercatorCoordinates extends BaseCoordinates
{

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param string $latColumn
     * @param string $lonColumn
     * @return string
     *
     * Применяем теорему Пифагора: здесь гипотенуза - расстояние точки от центра окружности
     * А катеты - расстояние вдоль параллели и меридиана
     */
    public function getCustomWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        $powedRadius = $radiusInMeters * $radiusInMeters;
        $meridianDegreeLength = self::DEGREE_LENGTH_IN_METERS;
        $parallelDegreeLength = self::DEGREE_LENGTH_IN_METERS * $this->getParallelMultiplier($lat);
        return <<<WHERE
        POW(({$latColumn} - {$lat}) * {$meridianDegreeLength}, 2) + 
        POW(({$lonColumn} - {$lon}) * {$parallelDegreeLength}, 2) < {$powedRadius}
WHERE;
    }

}