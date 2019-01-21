<?php
/**
 * @var string $apikey
 */

require 'resolve_request.php';
?>
<!DOCTYPE html>
<html>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://api-maps.yandex.ru/2.1/?apikey=<?= $apikey?>&lang=ru_RU" type="text/javascript">
</head>
<body>
<div id="map"></div>
</body>
</html>
