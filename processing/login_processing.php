<?php
    include_once(__DIR__ . "/../include/config.php");
    include_once(__DIR__ . "/../model/services/UserService.php");

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["email"]) || empty($_POST["password"])) {       
        redirect("/login.php");
    }

    try {
        // Convert form values into objects...

        // retrieve corresponding user
        $userService = UserService::getInstance();
        $user = $userService->findUserByEmail($_POST["email"]);
        
        if (!$user)
            throw new Exception("Informations erronées");

        // TODO: add link to resend email
        if ($userService->isLocked($user))
            throw new Exception("Vous devez valider votre compte. Merci de vérifier vos mails. (Pensez à regarder dans vos courriers indésirables)");

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

