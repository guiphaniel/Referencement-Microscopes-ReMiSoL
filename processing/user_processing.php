<?php
    include_once("../config/config.php");
    include_once("../model/services/UserService.php");

    if(!isUserSessionValid()) 
        redirect("/index.php");

    if(!$_SESSION["user"]["admin"] && $_POST["id"] != $_SESSION["user"]["id"])
        redirect("/account.php");

    function userRedirect() {
        if($_SESSION["user"]["admin"])
            redirect("/admin.php?action=users");
        else {
            redirect("/account.php?action=settings");
        }
    }

    if(empty($_POST["id"]) || empty($_POST["action"]))
        userRedirect();

    if ($_POST["action"] == "delete" && ($_SESSION["user"]["admin"] || $_POST["id"] == $_SESSION["user"]["id"])) {
        $userService = UserService::getInstance();
        $user = $userService->findUserById($_POST["id"]);
        if($userService->isAdmin($user) && sizeof($userService->findAllAdmins()) <= 1) {
            $_SESSION["form"]["errorMsg"] = "Il ne peut y avoir moins d'un administrateur.";
            userRedirect();
        } else {
            $userService->deleteUser($user);
            if($_POST["id"] == $_SESSION["user"]["id"])
                redirect("/processing/logout.php");
            else {
                $_SESSION["form"]["infoMsg"] = "L'utilisateur " . $user->getFirstname() . " " . $user->getLastname() . " a bien été supprimé.";
                redirect("/admin.php?action=users");
            }
        }
    }

    if($_POST["action"] != "update")
        userRedirect();

    if (empty($_POST["firstname"]) || empty($_POST["lastname"]) || empty($_POST["email"]) || empty($_POST["phoneCode"]) || empty($_POST["phoneNum"]))     
        userRedirect();

    // if a non-admin tries to modify admin fields, redirect
    if(!$_SESSION["user"]["admin"] && (!empty($_POST["admin"]) || !empty($_POST["locked"])))
        userRedirect();

    try {
        $id = $_POST["id"];

        // check if the password needs to be updated
        $hash;
        if(!empty($_POST["password1"]) && !empty($_POST["password2"])) {
            // check password validity
            if($_POST["password1"] !== $_POST["password2"])
                throw new Exception("Les mots de passe fournis ne correspondent pas");
            else
                $hash = password_hash($_POST["password1"], PASSWORD_DEFAULT);
        } else
            $hash = UserService::getInstance()->findUserById($id)->getPassword();

        //update user
        $user = (new User($_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["phoneCode"], substr($_POST["phoneNum"], -9), $hash, $_POST["locked"]??false, $_POST["admin"]??false))
            ->setId($id);

        UserService::getInstance()->updateUser($user);
    } catch (\Throwable $th) {
        $_SESSION["form"]["errorMsg"] = $th->getMessage();
        userRedirect();
    }
    
    $_SESSION["form"]["infoMsg"] = "Vos modifications ont bien été prises en compte.";
    userRedirect();