<?php 
    include_once("../config/config.php");
    include_once("../model/services/UserService.php");

    if(empty($_GET["id"]) || empty($_GET["token"]))
        redirect("/errors/422.php");

    if($_POST["password1"] !== $_POST["password2"]) {
        $_SESSION["form"]["errorMsg"] = "Les mots de passe fournis ne correspondent pas";
        redirect("/reset_password.php?id={$_GET["id"]}&token={$_GET["token"]}");
    }

    $userService = UserService::getInstance();

    // check the id's validity
    $user = $userService->findUserById($_GET["id"]);

    if($user == null)
        redirect("/errors/422.php");

    // check the token's validity
    $token = $userService->getLockedUserToken($user);

    if($token != $_GET["token"])
        redirect("/errors/422.php");

    // if everything's good, update the user's password... 
    $user->setPassword(password_hash($_POST["password1"], PASSWORD_DEFAULT));
    $userService->updateUser($user);

    //... and unlock him
    $userService->unlockUser($user);

    $_SESSION["form"]["infoMsg"] = "Votre mot de passe a bien été mis à jour. Vous pouvez désormais vous connecter.";
    redirect("/login.php");


    