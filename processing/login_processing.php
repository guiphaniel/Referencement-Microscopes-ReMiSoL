<?php
    include_once(__DIR__ . "/../include/config.php");
    include_once(__DIR__ . "/../model/services/UserService.php");

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["email"]) || empty($_POST["password"])) {       
        redirect("/login.php");
    }

    $userService = UserService::getInstance();
    try {
        // Convert form values into objects...

        // retrieve corresponding user
        $user = $userService->findUserByEmail($_POST["email"]);
        
        if (!$user)
            throw new Exception("Informations erronées");

        // TODO: add link to resend email
        if ($user->isLocked())
            throw new Exception("Informations erronées");

        //check password validity
        if(!password_verify($_POST["password"], $user->getPassword()))
            throw new Exception("Informations erronées");

    } catch (\Throwable $th) {
        $_SESSION["form"]["errorMsg"]=$th->getMessage();
        redirect("/login.php");
    }
    
    $reflector = new ReflectionClass("User");
    $properties = $reflector->getProperties();
    
    $_SESSION["user"]["id"] = $userService->getUserId($user);
    foreach($properties as $property) {
        $_SESSION["user"][$property->getName()] = $property->getValue($user);
    }

    header('location: /form.php');

