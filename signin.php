<?php 
    include_once("config/config.php");
    if(isUserSessionValid())
        redirect("/form.php");

    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    include_once("view/creators/FormCreator.php");

    $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

    $header = new HeaderCreator("Inscription"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page d'inscription au site.">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Inscription</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <?php FormCreator::handleMsg(); ?>
        <div class="form-wrapper">
            <form action="processing/signin_processing.php" method="post">
                <h2>Formulaire</h2>
                <div class="input-wrapper">
                    <input id="firstname" class="ucfirst" type="text" autocomplete="given-name" name="firstname" placeholder=" " required>
                    <label for="firstname">Prénom</label>
                </div>
                <div class="input-wrapper">
                    <input id="lastname" class="strtoupper" type="text" autocomplete="family-name" autocapitalize="characters" name="lastname" placeholder=" " required>
                    <label for="lastname">NOM</label>
                </div>
                <div class="input-wrapper">
                    <input id="email" type="email" autocomplete="email" name="email" placeholder=" " required>
                    <label for="email">Courriel</label>
                </div>
                <div class="select-input">
                    <select id="phone-code" name="phoneCode" autocomplete="tel-country-code">
                        <?php foreach ($phoneCodes as $code) : 
                            $value = substr($code, 0, strpos($code, ' '));?>
                            <option value=<?=$value;?> <?= $value == "+33" ? "selected" : "";?>><?=$code?></option>
                            <?php endforeach; ?>
                    </select>
                    <div class="input-wrapper">
                        <input id="phone" type="text" name="phoneNum" autocomplete="tel-national" pattern="0?\d{9}" placeholder=" " required>
                        <label for="phone">Télephone</label>
                    </div>
                </div>
                <div class="input-wrapper">
                    <input id="password1" type="password" autocomplete="new-password" name="password1" placeholder=" " required>
                    <label for="password1">Mot de passe</label>
                </div>
                <div class="input-wrapper">
                    <input id="password2" type="password" name="password2" placeholder=" " required>
                    <label for="password2">Vérification du mot de passe</label>
                </div>
                <input type="submit" class="bt">
            </form>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
    <script src="public/js/password_validation.min.js"></script>
</body>
</html>