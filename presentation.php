<?php 
    include_once("config/config.php");
    include_once("model/services/UserService.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");

    $header = new HeaderCreator("Présentation"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Présentation</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <h2>Historique</h2>
        <p>
            Le réseau des microscopies à sonde locale (<abbr title="Réseau des Microscopies à Sonde Locale">RéMiSoL</abbr>) est un réseau technologique implanté nationalement, qui a pour objet de fédérer les techniciens, ingénieurs et chercheurs ayant une expertise dans l’utilisation et/ou le développement de techniques de pointes.<br>
            C'est dans cette même cette démarche d'unité que s'incrit de ce site.
        </p>
        <h2>Objectifs</h2>
        <p>
            Ce site a pour but d'aider le référencement des microscopes présents sur le territoire, pour simplifier leur mise à disposition au reste de la communauté.<br>
            L'accès aux informations est libre, mais un compte est nécessaire pour référencer son propre matériel.
        </p>
        <h2>Fontionnement</h2>
        <p>
            Le site web est alimenté par la communauté. Chaque information est soumise au contrôle du comité, puis rendue accessible à tous, par le biais de la carte présente sur la page d'accueil, la barre de recherche, et l'<a href="">API REST</a>.
        </p>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>


    