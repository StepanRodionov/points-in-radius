<?php


namespace SR\GeoDataTest;


use SR\GeoDataTest\Base\BaseCoordinates;

class HaversineCoordinates extends BaseCoordinates
{
    const EARTH_RADIUS_IN_METERS = 6371000;

    public function getCustomWhere($lat, $lon, $radiusInMeters, $latColumn = 'LAT', $lonColumn = 'LON'): string
    {
        $earthRadius = self::EARTH_RADIUS_IN_METERS;
        $piBy180 = pi() / 180;
        return <<<WHERE
         6371000 * 2 * ASIN(
            SQRT(
                POWER(
                    SIN(
                        ({$lat} -  ABS({$latColumn})) * PI() / 180 / 2
                    )
                ), 2
            ) + 
            COS({$lat} * PI() / 180) *
            COS(ABS({$latColumn}) * PI() / 180) * 
            POWER(
                SIN(
                    ({$lon}, ABS({$lonColumn})) * PI() / 180 / 2
                ), 2
            )
        )
WHERE;
    }
}
