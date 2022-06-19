<?php 
    include_once("model/services/MicroscopesGroupService.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    include_once("view/creators/GroupDetailsCreator.php");

    if(empty($_GET["id"])) {
        header('location: /index.php');
        exit();
    }

    $group = MicroscopesGroupService::getInstance()->findMicroscopesGroupById($_GET["id"]); 
    if($group == null) {
        header('location: /index.php');
        exit();
    }

    if($group->isLocked() == true && !$_SESSION["user"]["admin"]) {
        header('location: /index.php');
        exit();
    }

    $header = new HeaderCreator("Détails"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page affichant les informations détaillées d'un groupe de microscopes.">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/MarkerCluster.css">
    <link rel="stylesheet" href="/public/css/MarkerCluster.Default.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
    <script src="/public/js/leaflet.markercluster.js"></script>
    <script src="/public/js/undo.min.js" defer></script>
    <title>Détails</title>
</head>
<body>
    <?php 
        $header->create()
    ?>
    <main>
        <?php (new GroupDetailsCreator($group, true, $_GET["microId"]??null))->create() ?>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
<script src="public/js/map.min.js"></script>
</html>