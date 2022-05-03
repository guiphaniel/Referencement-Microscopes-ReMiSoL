<?php 
    include_once("include/config.php");
    if(isUserSessionValid())
        redirect("/form.php");

    include_once("view/generators/HeaderCreator.php");
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
        <?php if(isset($_SESSION["login"]["errorMsg"])) : ?>
            <p id="error-msg"><?= $_SESSION["login"]["errorMsg"] ?></p>
        <?php endif; unset($_SESSION["login"]["errorMsg"]); ?>
        <form action="processing/login_processing.php" method="post">
            <label for="email">Courriel</label>
            <input id="email" type="email" autocomplete="email" name="email" required>
            <label for="password">Mot de passe</label>
            <input id="password" type="password" autocomplete="current-password" name="password" required>
            <input type="submit">
        </form>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>