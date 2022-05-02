<?php
    include_once(__DIR__ . "/../include/config.php");
    include_once(__DIR__ . "/../model/services/UserService.php");

    session_start();

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (!isset($_POST["email"]) || !isset($_POST["password"])) {       
        redirect("/login.php");
    }

    try {
        // Convert form values into objects...

        // retrieve corresponding user
        $user = UserService::getInstance()->findUserByEmail($_POST["email"]);
        
        if (!$user)
            throw new Exception("Informations erronées");

        //check password validity
        if(!password_verify($_POST["password"], $user->getPassword()))
            throw new Exception("Informations erronées");

    } catch (\Throwable $th) {
        $_SESSION["login"]["errorMsg"]=$th->getMessage();
        redirect("/login.php");
    }
    
    $reflector = new ReflectionClass("User");
    $properties = $reflector->getProperties();
    
    foreach($properties as $property) {
        $_SESSION["user"][$property->getName()] = $property->getValue($user);
    }

    header('location: /form.php');

