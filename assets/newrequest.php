<?php

use SR\GeoDataTest\Base\BaseCoordinates;
use SR\GeoDataTest\HaversineCoordinates;
use SR\GeoDataTest\MercatorCoordinates;
use SR\GeoDataTest\GeometryCoordinates;

try {
    //  Необходимо определить корректные настройки для подключения к БД
    $pdo = new PDO($dsn, $username, $passwd, $options);
    $geoPointsProvider = new MercatorCoordinates($pdo, 'points');

    $radius = $_REQUEST['radius'];
    $center = $_REQUEST['center'];

    $allPoints = $geoPointsProvider->getAllPoints();

    $time = microtime(true);
    $firstCirclePoints = $geoPointsProvider->getPoints($center[0], $center[1], $radius);
    $timeTaken = microtime(true) - $time;


    $pointsSet = [
        'points' => [
            'all' => $allPoints,
            'firstCircle' => $firstCirclePoints,
        ],
        'time' => $timeTaken,
    ];

    header("Content-type: Application/json");
    // echo $timeTaken; die();
    echo json_encode($pointsSet);
    die();
} catch (\Exception $e) {
    dump($e);
}