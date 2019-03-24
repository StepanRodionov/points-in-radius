<?php

namespace SR\GeoDataTest;

use SR\GeoDataTest\Base\BaseCoordinates;

class GeometryCoordinates extends BaseCoordinates
{
    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param string $latColumn
     * @param string $lonColumn
     * @return string
     *
     *
     */
    public function getCustomWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        return <<<WHERE
        ST_Distance(ST_GeomFromText('POINT({$lat} {$lon})', 4326), g) < {$radiusInMeters}
WHERE;

    }

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @param string $latColumn
     * @param string $lonColumn
     * @return string
     *
     * 5 точек в полигоне: 4 для каждого из углов квадрата и пятая для того, чтобы замкнуть его
     * повторяет первую точку
     */
    public function getSqlSquareWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        $meridianLengthInDegrees = $radiusInMeters / self::DEGREE_LENGTH_IN_METERS;
        $parallelLengthInDegrees = $radiusInMeters / (self::DEGREE_LENGTH_IN_METERS * $this->getParallelMultiplier($lat));
        $upperPart = $lat + $meridianLengthInDegrees;
        $lowerPart = $lat - $meridianLengthInDegrees;
        $leftPart = $lon - $parallelLengthInDegrees;
        $rightPart = $lon + $parallelLengthInDegrees;

        return <<<WHERE
        (ST_WITHIN(
            g, 
            ST_GeomFromText(
                "POLYGON(
                    (
                        {$upperPart} {$leftPart},
                        {$upperPart} {$rightPart},
                        {$lowerPart} {$rightPart}, 
                        {$lowerPart} {$leftPart},
                        {$upperPart} {$leftPart}
                    )
                )", 4326
            )
        ))
WHERE;

    }


}