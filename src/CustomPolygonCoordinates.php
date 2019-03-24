<?php


namespace SR\GeoDataTest;

use SR\GeoDataTest\Base\BaseCoordinates;

class CustomPolygonCoordinates extends BaseCoordinates
{
    const TYPE_GEOJSON = 'json';

    const TYPE_GEOPOLYGON = 'polygon';

    /**
     * @param $lat
     * @param $lon
     * @param $radiusInMeters
     * @return array|null
     */
    public function getPoints($lat, $lon, $radiusInMeters, $polygon): ?array
    {
        $sql = <<<SQL
    SELECT id, lat, lon from {$this->tableName} 
    where {$this->getPolygonWhere($polygon)}
    order by id
SQL;
        return $this->query($sql);
    }

    /**
     * @param array $polygon
     * @param string $type
     * @return string
     */
    public function getPolygonWhere(array $polygon, string $type = self::TYPE_GEOJSON): string
    {
        if($type === self::TYPE_GEOJSON){
            return $this->createGeoJsonWhere($polygon);
        } else {
            return $this->createGeoPolygonWhere($polygon);
        }
    }

    /**
     * @param $polygon
     * @return string
     */
    private function createGeoJsonWhere($polygon): string
    {
        return '1 = 1';
    }

    /**
     * @param $polygon
     * @return string
     */
    private function createGeoPolygonWhere($polygon): string
    {
        return '1 = 1';
    }
}