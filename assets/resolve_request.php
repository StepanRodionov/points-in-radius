<?php

use SR\GeoDataTest\Base\BaseCoordinates;
use SR\GeoDataTest\HaversineCoordinates;
use SR\GeoDataTest\MercatorCoordinates;
use SR\GeoDataTest\GeometryCoordinates;

try {
    //  Необходимо определить корректные настройки для подключения к БД
    $pdo = new PDO($dsn, $username, $passwd, $options);
    $geoPointsProvider = new GeometryCoordinates($pdo, 'points');


    $allPoints = $geoPointsProvider->getAllPoints();
    $time = microtime(true);
    $firstCirclePoints = $geoPointsProvider->getPoints(55.751374, 37.616758, 2000);
    $secondCirclePoints = $geoPointsProvider->getPoints(55.849967, 37.439600, 3000);
    $thirdCirclePoints = $geoPointsProvider->getPoints(55.683455, 37.622609, 4000);
    $timeTaken = microtime(true) - $time;


    $pointsSet = [
        'points' => [
            'all' => $allPoints,
            'firstCircle' => $firstCirclePoints,
            'secondCircle' => $secondCirclePoints,
            'thirdCircle' => $thirdCirclePoints,
        ],
        'time' => $timeTaken,
    ];

    header("Content-type: Application/json");
    // echo $timeTaken; die();
    // echo json_encode($pointsSet);
    die();

    //  Тест на то, одинаковые ли результаты получились при выборке точек. На 3000 штук получились идентичные точки
    $a = new HaversineCoordinates($pdo, 'points');
    $b = new MercatorCoordinates($pdo, 'points');
    $c = new GeometryCoordinates($pdo, 'points');
    function getPointsString(BaseCoordinates $geoPointsProvider){
        $firstCirclePoints = $geoPointsProvider->getPoints(55.751374, 37.616758, 2000);
        $secondCirclePoints = $geoPointsProvider->getPoints(55.849967, 37.439600, 3000);
        $thirdCirclePoints = $geoPointsProvider->getPoints(55.683455, 37.622609, 4000);

        return ([
            'firstCircle' => $firstCirclePoints,
            'secondCircle' => $secondCirclePoints,
            'thirdCircle' => $thirdCirclePoints,
        ]);
    }

    $as = getPointsString($a);
    $bs = getPointsString($b);
    $cs = getPointsString($c);

    echo json_encode([
        md5(json_encode($as)),
        md5(json_encode($bs)),
        md5(json_encode($cs)),
        $as,
        $bs,
        $cs
    ]);

} catch (\Exception $e) {
    dump($e);
}