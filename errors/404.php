<?php 
    include_once("../config/config.php");
    include_once("../view/creators/HeaderCreator.php");
    include_once("../view/creators/FooterCreator.php");

    http_response_code(404);

    $header = new HeaderCreator("Erreur 404"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="La ressource demandée n'a pas été trouvée">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Erreur 404</title>
</head>
<body>
    <?php $header->create(); ?>
    <main>
        <div class="msg-wrapper">
            <div class="msg error-msg">
                <p>Oups ! il semblerait que vous vous soyez égaré !</p> 
                <p>Le page que vous cherchez n'a pas été trouvée. Pour revenir à l'accueil, <a href="/index.php">cliquez ici</a>.</p>   
            </div>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>


    