<?php 
    include_once("include/config.php");
    include_once("view/generators/HeaderCreator.php");
    include_once("view/generators/UserFormCreator.php");
    include_once("view/generators/GroupDetailsCreator.php");
    include_once("model/services/UserService.php");
    include_once("model/services/MicroscopesGroupService.php");

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
        <h2>Utilisateurs</h2>
        <div id="user-forms">
            <?php foreach(UserService::getInstance()->findAllUsers() as $user)
                (new UserFormCreator($user, true))->create();
            ?>
        </div>
        <h2>Fiches</h2>
        <div id="micro-group-forms">
            <?php 
                foreach(MicroscopesGroupService::getInstance()->findAllMicroscopesGroup() as $group)
                    (new GroupDetailsCreator($group, false))->create();
            ?>
        </div>
        <h2>Mots-clés</h2>
        <h2>Microscopes</h2>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
    <script src="public/js/user_form.js"></script>
</body>
</html>