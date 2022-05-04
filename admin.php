<?php 
    include_once("include/config.php");
    include_once("view/generators/HeaderCreator.php");
    include_once("view/generators/UserFormCreator.php");
    include_once("model/services/UserService.php");

    if(!isUserSessionValid() || !$_SESSION["user"]["admin"]) 
        redirect("/index.php");

    $header = new HeaderCreator("Administration"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Administration</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <?php foreach(UserService::getInstance()->findAllUsers() as $user)
            (new UserFormCreator("processing/user_processing.php", "post", "", $user, true))->create();
        ?>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>