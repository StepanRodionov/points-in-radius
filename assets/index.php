<?php

use Main\DataBase\Connection;
use Main\Enum\Variables;

require '../../vendor/autoload.php';

$pdo = Connection::getInstance()->getConnection();

$res = $pdo->query("show tables;");


?>
<!DOCTYPE html>
<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://api-maps.yandex.ru/2.1/?apikey=<?= Variables::YANDEX_MAPS_KEY ?>&lang=ru_RU"
            type="text/javascript"></script>
    <title>Тестируем геоданные</title>
</head>
<body>
<center>
    <div id="map" style="width: 1200px; height: 640px"></div>
    <br>
    <button id="start">
        Получить точки
    </button>
    <div class="result"></div>
</center>
</body>

<script type="text/javascript">
    ymaps.ready(init);
    function init(){
        var myMap = new ymaps.Map("map", {
            center: [55.753215, 37.622504],
            zoom: 9
        });

        const pointsSets = {
            all: {color: 'blue'},
            firstCircle: {color: 'red', radius: 2000, center: [55.751374, 37.616758], circleColor: '#ff0000'},
            secondCircle: {color: 'darkGreen', radius: 3000, center: [55.849967, 37.439600], circleColor: '#00ff00'},
            thirdCircle: {color: 'black', radius: 4000, center: [55.683455, 37.622609], circleColor: '#000000'}
        };

        var collections = {};

        for(let key in pointsSets){
            col = new ymaps.GeoObjectCollection(null, {preset: 'islands#'+pointsSets[key]['color']+'CircleDotIcon'});
            collections[key] = col;
            myMap.geoObjects.add(col);
        }

        (function () {
            $('#start').click(function () {
                $.ajax({
                    url: '/src/api/geo/resolve_request.php',
                    success: function(points){
                        for(let key in pointsSets){
                            let collection = collections[key];
                            let currentSet = points['points'][key];
                            let pointSetData = pointsSets[key];
                            for (let a in currentSet){
                                let item = currentSet[a];
                                let coords = [item.lat, item.lon];
                                let placemark = new ymaps.Placemark(coords, { balloonContent: 'Точка ' + item.id });
                                collection.add(placemark);
                            }
                            if(!pointSetData.radius){
                                continue;
                            }
                            var myCircle = new ymaps.Circle([
                                pointSetData.center,
                                pointSetData.radius
                            ], {
                                balloonContent: "Круг для контроля попадания точек",
                            }, {
                                fillColor: pointSetData.circleColor,
                                fillOpacity: 0.3,

                                strokeColor: pointSetData.circleColor,
                                strokeOpacity: 0.8,
                                strokeWidth: 3
                            });

                            myMap.geoObjects.add(myCircle);
                        }
                        $('.result').html('Время работы скрипта ' + points.time + ' секунд');
                    }
                });
            });
        }());
    }


</script>

</html>