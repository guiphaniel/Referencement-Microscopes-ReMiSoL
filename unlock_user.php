<?php 
    include_once("include/config.php");
    include_once("model/services/userService.php");

    if(!isset($_GET["id"]) || !isset($_GET["token"]))
        redirect("/index.php");

    $userService = UserService::getInstance();

    // check the id's validity
    $user = $userService->findUserById($_GET["id"]);

    if($user == null)
        redirect("/index.php");

    // check the token's validity
    $token = $userService->getLockedUserToken($user);

    if($token != $_GET["token"])
        redirect("/index.php");

    // if everything's good, unlock the user
    $userService->unlockUser($user);

    redirect("/login.php");


    