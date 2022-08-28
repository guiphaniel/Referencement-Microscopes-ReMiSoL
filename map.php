<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page d'accueil du site. Carte du monde des microscopes et filtres.">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/MarkerCluster.css">
    <link rel="stylesheet" href="/public/css/MarkerCluster.Default.css">
    <script src="/public/js/leaflet.markercluster.js"></script>
    <link rel="stylesheet" href="/public/css/style.min.css">
    <title>Carte</title>
</head>
<body>
    <div id="map-wrapper" class="index-map-wrapper" style="margin: 0;">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="def">            
            <defs>
                <path id="marker" d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z"/>
            </defs>
        </svg>
        <div id="map" style="height: 100vh;"></div>
    </div>
    <script src="public/js/map.min.js"></script>
</body>
</html>