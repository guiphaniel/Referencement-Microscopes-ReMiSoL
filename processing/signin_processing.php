<?php
    include_once(__DIR__ . "/../model/services/UserService.php");

    session_start();

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["firstname"]) || empty($_POST["lastname"]) || empty($_POST["email"]) || empty($_POST["phone"]) || empty($_POST["phoneCode"]) || empty($_POST["password1"]) || empty($_POST["password2"])) {       
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
        $userService = UserService::getInstance();
        $userService->save($user);
        $token = $userService->lockUser($user);

        // send verification mail
        $headers = "";
        $headers .= "Reply-To: David Albertini <noreply@guilhem.davidalbertini.fr>\r\n"; 
        $headers .= "Return-Path: David Albertini <noreply@guilhem.davidalbertini.fr>\r\n"; 
        $headers .= "From: David Albertini <noreply@guilhem.davidalbertini.fr>\r\n";  
        $headers .= "Organization: David Albertini\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=utf-8\r\n";
        $headers .= "X-Priority: 3\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n" ;

        mail($user->getEmail(), "Activation de votre compte", "Bonjour,\n\nAfin d'activer votre compte, veuillez suivre le lien suivant : https://guilhem.davidalbertini.fr/unlock_user.php?id=$id&token=$token.\n\nA bientôt.\n\n\n Ce mail est un mail automatique, merci de ne pas y répondre.", $headers);
    } catch (\Throwable $th) {
        $_SESSION["signin"]["errorMsg"]=$th->getMessage();
        header('location: /signin.php');
        exit();
    }
    
    header('location: /login.php');

