<?php 
    include_once("config/config.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    include_once("view/creators/GroupFormCreator.php");
    include_once("model/services/KeywordService.php");
    include_once("model/services/CompagnyService.php");
    include_once("utils/normalize_utf8_string.php");

    $labTypes = ["UPR", "UMR", "IRL", "UAR", "FR", "EMR"];
    $countries = ["Belgique", "France", "Suisse"]; // Belgium, France, Switzerland
    $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

    if(!isUserSessionValid()) 
        redirect("/index.php");

    $header = new HeaderCreator("Formulaire"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page permettant la création d'une fiche de référencement d'un groupe de microscopes.">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="stylesheet" href="/public/css/MarkerCluster.css">
    <link rel="stylesheet" href="/public/css/MarkerCluster.Default.css">
    <script src="/public/js/leaflet.markercluster.js"></script>
    <title>Formulaire</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <?php (new GroupFormCreator())->create(); ?>
    </main>
    <?php (new FooterCreator)->create() ?>
    <script src="public/js/form.min.js"></script>
    <script src="public/js/map.min.js"></script>
</body>
</html>