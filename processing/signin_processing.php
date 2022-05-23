<?php
    include_once(__DIR__ . "/../model/services/UserService.php");
    include_once(__DIR__ . "/../utils/send_email.php");

    session_start();

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["firstname"]) || empty($_POST["lastname"]) || empty($_POST["email"]) || empty($_POST["phoneCode"]) || empty($_POST["phoneNum"]) || empty($_POST["password1"]) || empty($_POST["password2"])) {       
        header('location: /signin.php');
        exit();
    }

    try {
        // Convert form values into objects...

        //check password validity
        if($_POST["password1"] !== $_POST["password2"])
            throw new Exception("Les mots de passe fournis ne correspondent pas");

        $user = new User($_POST["firstname"], $_POST["lastname"], $_POST["email"], $_POST["phoneCode"], substr($_POST["phoneNum"], -9), password_hash($_POST["password1"], PASSWORD_DEFAULT));
        
        // save the user into the db
        $userService = UserService::getInstance();
        $id = $userService->save($user);
        $token = $userService->lockUser($user);

        // send verification mail
        $object = "[RéMiSoL] Activation de votre compte";
        $content = "Bonjour,\n\nAfin d'activer votre compte, veuillez suivre le lien suivant : https://" . WEBSITE_URL . "/processing/unlock_user.php?id=$id&token=$token\n\nA bientôt.\n\n\n Ce mail est un mail automatique, merci de ne pas y répondre.";
        sendEmail($user->getEmail(), $object, $content);
    } catch (\Throwable $th) {
        $_SESSION["form"]["errorMsg"]=$th->getMessage();
        header('location: /signin.php');
        exit();
    }
    
    header('location: /login.php');

