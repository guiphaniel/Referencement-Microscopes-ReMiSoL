<?php
    include_once(__DIR__ . "/../config/config.php");
    include_once(__DIR__ . "/../utils/send_email.php");

    //verify that all fields were sent by the form TODO: if not, store values in session to prefill the form
    if (empty($_POST["firstname"]) || empty($_POST["lastname"]) || empty($_POST["email"]) || empty($_POST["subject"]) || empty($_POST["content"]))     {
        $_SESSION["form"]["errorMsg"] = "Veuillez renseigner l'intégralité des champs.";
        redirect("/contact.php");
    }

    if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $_SESSION["form"]["errorMsg"] = "Veuillez saisir un courriel valide.";
        redirect("/contact.php");
    }

    $subject = "[RéMiSoL] ";
    $subject .= $_POST["subject"];

    $content = "Bonjour,\n\nVous avez reçu un nouveau message de {$_POST["firstname"]} {$_POST["lastname"]} ({$_POST["email"]}).\n\n\n";
    $content .= $_POST["subject"] ."\n\n";
    $content .= $_POST["content"];

    try {
        if(@sendEmail(WEB_MASTER_EMAIL, $subject, $content))
            $_SESSION["form"]["infoMsg"] = "Votre message a bien été envoyé, vous recevrez une réponse sous peu. Nous vous remercions pour votre confiance.";
        else
            $_SESSION["form"]["errorMsg"] = "Une erreur est survenue lors de l'envoi de votre message, veuillez réessayer dans un instant.\nNous nous excusons pour la gène occasionnée.";
    } catch (Exception $e) {
        $_SESSION["form"]["errorMsg"] = "Une erreur est survenue lors de l'envoi de votre message, veuillez réessayer dans un instant.\nNous nous excusons pour la gène occasionnée.";
    }

    header('location: /contact.php');

