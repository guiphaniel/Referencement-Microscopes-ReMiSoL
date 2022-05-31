<?php
    include_once(__DIR__ . "/../config/config.php");
    include_once(__DIR__ . "/../utils/send_email.php");
    include_once(__DIR__ . "/../model/services/UserService.php");

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["email"])) {       
        $_SESSION["form"]["errorMsg"] = "veuillez saisir un courriel";
        redirect("/password_forgotten.php");
    }

    $userService = UserService::getInstance();
    // Convert form values into objects...

    // retrieve corresponding user
    $user = $userService->findUserByEmail($_POST["email"]);

    if ($user) {
        $token = UserService::getInstance()->lockUser($user);
        $id = $user->getId();

        // send reset mail
        $object = "[RéMiSoL] Réinitialisation de votre mot de passe";
        $content = "Bonjour,\n\nVotre demande de réinitialisation de mot de passe à bien été prise en compte. Pour finaliser la procédure, veuillez suivre le lien suivant : https://" . WEBSITE_URL . "/reset_password.php?id=$id&token=$token\n\nA bientôt.\n\n\n Ce courriel est un courriel automatique, merci de ne pas y répondre.";
        sendEmail($user->getEmail(), $object, $content);
    }

    $_SESSION["form"]["infoMsg"] = "Nous avons bien enregistré votre demande de réinitialisation de mot de passe. Si le courriel que vous nous avez communiqué correspond à un compte enregistré sur " . WEBSITE_URL . ", un message vous a été envoyé.";
    redirect("/password_forgotten.php");

