<?php 
    include_once("config/config.php");

    if(isUserSessionValid())
        redirect("/form.php");

    include_once("view/creators/FormCreator.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    $header = new HeaderCreator("Connexion"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de connexion au compte utilisateur.">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Connexion</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <?php FormCreator::handleMsg(); ?>
        <div class="form-wrapper">
            <form action="processing/login_processing.php" method="post">
            <div class="input-wrapper">
                <input id="email" type="email" autocomplete="email" name="email" placeholder=" " required>
                <label for="email">Courriel</label>
            </div>
            <div class="input-wrapper">
                <input id="password" type="password" autocomplete="current-password" name="password" placeholder=" " required>
                <label for="password">Mot de passe</label>
            </div>
                <input type="submit" class="bt">
            </form>
            <a href="signin.php">Pas encore de compte ?</a>
            <a href="password_forgotten.php">Mot de passe oublié ?</a>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>