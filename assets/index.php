<?php


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
    <div class="buttons">
        <button id="start">
            Получить круги с точками
        </button>
        <span>
            <input type="radio" id="typePoints0" name="typePoints" value="0" checked /> <label for="typePoints0">Восток</label>
            <input type="radio" id="typePoints1" name="typePoints" value="1" /> <label for="typePoints1">Юг</label>
            <input type="radio" id="typePoints2" name="typePoints" value="2" /> <label for="typePoints2">Северо-Запад</label>
            <input type="radio" id="typePoints3" name="typePoints" value="3" /> <label for="typePoints3">Юго-Запад</label>
        </span>
        <button id="errors">
            Получить большой круг
        </button>
    </div>
    <div>
        <button id="clear">
            Очистить карту
        </button>
    </div>
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

        var pointsSets = {
            all: {color: 'blue'},
            firstCircle: {color: 'red', radius: 2000, center: [55.751374, 37.616758], circleColor: '#ff0000'},
            secondCircle: {color: 'darkGreen', radius: 3000, center: [55.849967, 37.439600], circleColor: '#00ff00'},
            thirdCircle: {color: 'black', radius: 4000, center: [55.683455, 37.622609], circleColor: '#000000'}
        };


        var collections = {};



        (function () {
            const longRadiusCircle = [
                {color: 'red', radius: 100000, center: [55.762121, 39.204793], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [54.851360, 37.665257], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [56.273250, 36.314308], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [55.351073, 36.196440], circleColor: '#ff0000'},
                {color: 'red', radius: 100000, center: [56.650848, 37.644335], circleColor: '#ff0000'}
            ];

            var pointsSetsSmall = {
                all: {color: 'blue'},
                firstCircle: {}
            };


            $('#start').click(function () {
                $.ajax({
                    url: '/src/api/geo/resolve_request.php',
                    success: function(points){
                        drawPoints(points, pointsSets);
                    }
                });
            });

            $('#errors').click(function () {
                let circleType = $('input[name=typePoints]:checked').val();
                pointsSetsSmall.firstCircle = longRadiusCircle[circleType];
                $.ajax({
                    url: '/src/api/geo/newrequest.php',
                    data: {
                        radius: pointsSetsSmall.firstCircle.radius,
                        center: pointsSetsSmall.firstCircle.center
                    },
                    success: function(points){
                        drawPoints(points, pointsSetsSmall);
                    }
                });
            });

            $('#clear').click(function(){
                myMap.geoObjects.removeAll();
                addCollections();
            });


            function drawPoints(points, pointsSets){
                for(let key in pointsSets){
                    let collection = collections[key];
                    let currentSet = points['points'][key];
                    let pointSetData = pointsSets[key];
                    console.log(pointSetData);
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
                        geodesic: true,
                        strokeColor: pointSetData.circleColor,
                        strokeOpacity: 0.8,
                        strokeWidth: 3
                    });

                    myMap.geoObjects.add(myCircle);
                }
                $('.result').html('Время работы скрипта ' + points.time + ' секунд');
            }

            function addCollections(){
                for(let key in pointsSets){
                    col = new ymaps.GeoObjectCollection(null, {preset: 'islands#'+pointsSets[key]['color']+'CircleDotIcon'});
                    collections[key] = col;
                    myMap.geoObjects.add(col);
                }
            }
            addCollections();
        }());
    }


</script>

</html>