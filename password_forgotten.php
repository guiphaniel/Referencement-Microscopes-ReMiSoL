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
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Mot de passe oublié</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <div class="infos">
            <p>Saisissez votre courriel. Vous allez recevoir un message vous permettant de modifier votre mot de passe.</p>
        </div>
        <?php FormCreator::handleMsg(); ?>
        <div class="form-wrapper">
            <form action="processing/password_forgotten_processing.php" method="post">
                <div class="input-wrapper">    
                    <input id="email" type="email" autocomplete="email" name="email" placeholder=" " required>
                    <label for="email">Courriel</label>
                </div>
                    <input type="submit" value="Envoyer" class="bt">
            </form>
        </div>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>