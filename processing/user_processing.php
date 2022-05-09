<?php
    include_once("../include/config.php");
    include_once("../model/services/UserService.php");

    function userRedirect() {
        if($_SESSION["user"]["admin"])
            redirect("/admin.php");
        else
            redirect("/index.php"); // TODO: replace by account
    }

    if(empty($_POST["id"]) || empty($_POST["action"]))
        userRedirect();

    if ($_POST["action"] == "delete") {
        UserService::getInstance()->deleteUser($_POST["id"]);
        userRedirect();
    }

    if($_POST["action"] != "update")
        userRedirect();

    if (empty($_POST["firstname"]) || empty($_POST["lastname"]) || empty($_POST["email"]) || empty($_POST["phoneCode"]) || empty($_POST["phone"]))     
        userRedirect();

    if(!$_SESSION["user"]["admin"] && $_POST["id"] != $_SESSION["user"]["id"])
        redirect("/index.php"); // TODO: replace by account

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
        $user = new User($_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["phoneCode"], $_POST["phoneNum"], $hash, $_POST["locked"]??false, $_POST["admin"]??false);

        UserService::getInstance()->updateUser($id, $user);
    } catch (\Throwable $th) {
        $_SESSION["form"]["errorMsg"] = $th->getMessage();
        userRedirect();
    }
    
    userRedirect();