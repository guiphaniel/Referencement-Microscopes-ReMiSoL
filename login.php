<?php 
    include_once("config/config.php");

    if(isUserSessionValid())
        redirect("/form.php");

    include_once("view/creators/FormCreator.php");
    include_once("view/creators/HeaderCreator.php");
    $header = new HeaderCreator("Connexion"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <?php FormCreator::handleMsg(); ?>
        <form action="processing/login_processing.php" method="post">
            <label for="email">Courriel</label>
            <input id="email" type="email" autocomplete="email" name="email" required>
            <label for="password">Mot de passe</label>
            <input id="password" type="password" autocomplete="current-password" name="password" required>
            <input type="submit">
        </form>
        <a href="signin.php">Pas encore de compte ?</a>
        <a href="password_forgotten.php">Mot de passe oublié ?</a>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>