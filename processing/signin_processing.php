<?php
    include_once(__DIR__ . "/../model/services/UserService.php");

    session_start();

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (!isset($_POST["firstname"]) || !isset($_POST["lastname"]) || !isset($_POST["email"]) || !isset($_POST["phone"]) || !isset($_POST["phoneCode"]) || !isset($_POST["password1"]) || !isset($_POST["password2"])) {       
        header('location: /signin.php');
        exit();
    }

    try {
        // Convert form values into objects...

        //check password validity
        if($_POST["password1"] !== $_POST["password2"])
            throw new Exception("Les mots de passe fournis ne correspondent pas");

        $_POST["phone"] = $_POST["phoneCode"] . " " . substr($_POST["phone"], -9);
        $user = new User($_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["phone"], password_hash($_POST["password1"], PASSWORD_DEFAULT));
        
        // save the user into the db
        UserService::getInstance()->save($user);
    } catch (\Throwable $th) {
        $_SESSION["signin"]["errorMsg"]=$th->getMessage();
        header('location: /signin.php');
        exit();
    }
    
    header('location: /login.php');

