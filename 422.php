<?php 
    include_once("config/config.php");
    include_once("model/services/UserService.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");

    http_response_code(422);

    $header = new HeaderCreator("Erreur 422"); 
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
    <title>Erreur 422</title>
</head>
<body>
    <?php $header->create(); ?>
    <main>
        <div class="msg-wrapper">
            <div class="msg error-msg">
                <p>Une erreur est survenue.</p> 
                <p>Si le problème persiste, n'hésitez pas à <a href="/contact.php">nous contacter</a>.</p>   
            </div>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>


    