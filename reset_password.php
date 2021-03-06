<?php 
    include_once("config/config.php");
    include_once("view/creators/FormCreator.php");
    include_once("model/services/UserService.php");

    if(isUserSessionValid())
        redirect("/form.php");

    if(empty($_GET["id"]) || empty($_GET["token"]))
        redirect("/index.php");

    $userService = UserService::getInstance();

    // check the id's validity
    $user = $userService->findUserById($_GET["id"]);

    if($user == null)
        redirect("/index.php");

    // check the token's validity
    $token = $userService->getLockedUserToken($user);

    if(empty($token)) {
        $_SESSION["form"]["infoMsg"] = "Vous avez déjà mis votre mot de passe à jour.\n Vous pouvez désormais vous connecter.";
        redirect("/login.php");
    }

    if($token != $_GET["token"])
        redirect("/status/422.php");

    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    $header = new HeaderCreator("Réinitialisation du mot de passe"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page permettant de finaliser la demande de réinitialisation du mot de passe, en demandant de saisir le nouveau.">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Réinitialisation du mot de passe</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <?php FormCreator::handleMsg(); ?>
        <div class="form-wrapper">
            <form action="processing/reset_password_processing.php?id=<?=$_GET["id"]?>&token=<?=$_GET["token"]?>" method="post">
                <div class="input-wrapper">    
                    <input id="password1" type="password" autocomplete="new-password" name="password1" placeholder=" " required>
                    <label for="password1">Nouveau mot de passe</label>
                </div>
                <div class="input-wrapper">
                    <input id="password2" type="password" name="password2" placeholder=" " required>
                    <label for="password2">Vérification du mot de passe</label>
                </div>
                <input type="submit" class="bt">
            </form>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
    <script src="public/js/password_validation.min.js"></script>
</body>
</html>