<?php 
    include_once("../config/config.php");
    include_once("../model/services/UserService.php");
    include_once("../view/creators/FormCreator.php");
    include_once("../view/creators/HeaderCreator.php");
    $header = new HeaderCreator("Validation du compte"); 

    if(!unlockUser())
        redirect("/422.php");
    
    function unlockUser() {
        if(empty($_GET["id"]) || empty($_GET["token"]))
            return false;

        $userService = UserService::getInstance();

        // check the id's validity
        $user = $userService->findUserById($_GET["id"]);

        if($user == null)
            return false;

        // check the token's validity
        $token = $userService->getLockedUserToken($user);

        if($token != $_GET["token"])
            return false;

        // if everything's good, unlock the user
        $userService->unlockUser($user);

        return true;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <title>Validation du compte</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <p>Votre compte a été activé avec succès !</p>
        <p>Vous pouvez fermer cette page en toute sécurité</p>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>


    