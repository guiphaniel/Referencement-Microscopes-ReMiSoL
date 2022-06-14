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

        if(!isset($token)) {
            $_SESSION["form"]["errorMsg"] = "Votre compte est actuellement en attente de validation.\n Merci de vous assurer que vous n'avez pas déjà reçu un précédent mail de notre part vous permettant de valider votre compte.\n Sinon, nous vous invitons à <a href='/contact.php'>nous contacter</a>.";
            redirect("/password_forgotten.php");
        }

        $id = $user->getId();

        // send reset mail
        $subject = "[RéMiSoL] Réinitialisation de votre mot de passe";
        $content = "Bonjour,\n\nVotre demande de réinitialisation de mot de passe à bien été prise en compte. Pour finaliser la procédure, veuillez suivre le lien suivant : https://" . WEBSITE_URL . "/reset_password.php?id=$id&token=$token\n\nA bientôt.\n\n\n Ce courriel est un courriel automatique, merci de ne pas y répondre.";
        sendEmail($user->getEmail(), $subject, $content);
    }

    $_SESSION["form"]["infoMsg"] = "Nous avons bien enregistré votre demande de réinitialisation de mot de passe. Si le courriel que vous nous avez communiqué correspond à un compte existant, un message vous a été envoyé.";
    redirect("/password_forgotten.php");

