<?php 
    include_once("config/config.php");
    if(isUserSessionValid())
        redirect("/account.php");

    include_once("view/creators/FormCreator.php");
    include_once("view/creators/HeaderCreator.php");
    $header = new HeaderCreator("Mot de passe oublié"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <p>Saisissez votre courriel. Vous allez recevoir un message vous permettant de modifier votre mot de passe.</p>
        <?php FormCreator::handleMsg(); ?>
        <form action="processing/password_forgotten_processing.php" method="post">
            <label for="email">Courriel</label>
            <input id="email" type="email" autocomplete="email" name="email" required>
            <input type="submit" value="Envoyer">
        </form>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>