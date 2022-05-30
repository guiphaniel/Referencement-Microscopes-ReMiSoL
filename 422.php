<?php 
    include_once("include/config.php");
    include_once("model/services/UserService.php");
    include_once("view/generators/HeaderCreator.php");

    http_response_code(422);

    $header = new HeaderCreator("Erreur 422"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erreur 422</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <?php
            echo "<p>Une erreur est survenue.</p>";
            $adminEmail = UserService::getInstance()->findAllAdmins()[0]->getEmail();
            echo "<p>Si le problème persiste, veuillez contacter un administrateur à l'addresse suivante : $adminEmail</p>";
        ?>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>


    