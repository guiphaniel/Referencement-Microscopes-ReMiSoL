<?php 
    include_once("config/config.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    include_once("view/creators/UserFormCreator.php");
    include_once("view/creators/GroupFormCreator.php");
    include_once("model/services/UserService.php");
    include_once("model/services/MicroscopesGroupService.php");

    if(!isUserSessionValid() || MicroscopesGroupService::getInstance()->findGroupOwner($_GET["id"])?->getId() != $_SESSION["user"]["id"]) 
        redirect("/index.php");

    $header = new HeaderCreator("Édition"); 
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
    <title>Édition</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <?php 
            (new GroupFormCreator(MicroscopesGroupService::getInstance()->findMicroscopesGroupById($_GET["id"])))->create();
        ?>
    </main>
    <?php (new FooterCreator)->create() ?>
    <script src="public/js/form.js"></script>
</body>
</html>