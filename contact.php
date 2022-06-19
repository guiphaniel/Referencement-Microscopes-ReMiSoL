<?php 
    include_once("config/config.php");

    include_once("view/creators/FormCreator.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    $header = new HeaderCreator("Nous contacter"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page de contact avec un formulaire permettant de contacter le support.">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Nous contacter</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <div class="infos">
            <p>Pour toute demande nous concernant, n'hésitez pas à nous contacter en remplissant ce formulaire.</p>
            <p>Conformément à notre <a href="/legal-infos.php">politique relative au traitement des données personnelles</a>, les informations renseignées ci-dessous ne seront concervées que durant la période de traitement de votre demande.</p>
        </div>
        <?php FormCreator::handleMsg(); ?>
        <div class="form-wrapper">
            <form action="/processing/contact_processing.php" method="post">
            <div class="input-wrapper">
                <input id="firstname" class="ucfirst" type="text" autocomplete="given-name" name="firstname" placeholder=" " <?= isset($_SESSION["user"]) ? "value={$_SESSION["user"]["firstname"]}" : "" ?> required>
                <label for="firstname">Prénom</label>
            </div>
            <div class="input-wrapper">
                <input id="lastname" class="strtoupper" type="text" autocomplete="family-name" autocapitalize="characters" name="lastname" placeholder=" " <?= isset($_SESSION["user"]) ? "value={$_SESSION["user"]["lastname"]}" : "" ?> required>
                <label for="lastname">NOM</label>
            </div>
            <div class="input-wrapper">
                <input id="email" type="email" autocomplete="email" name="email" placeholder=" "  <?= isset($_SESSION["user"]) ? "value={$_SESSION["user"]["email"]}" : "" ?> required>
                <label for="email">Votre courriel</label>
            </div>
            <div class="input-wrapper">
                <input id="subject" type="text" name="subject" placeholder=" " required>
                <label for="subject">Objet</label>
            </div>
            <div class="input-wrapper">
                <textarea id="content" name="content" cols="30" rows="10" placeholder=" " required></textarea>
                <label for="content">Corps du message</label>
            </div>
                <input type="submit" class="bt">
            </form>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>