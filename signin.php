<?php 
    include_once("include/config.php");
    if(isUserSessionValid())
        redirect("/form.php");

    include_once("view/generators/HeaderCreator.php");

    $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

    $header = new HeaderCreator("Inscription"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <?php if(isset($_SESSION["signin"]["errorMsg"])) : ?>
            <p id="error-msg"><?= $_SESSION["signin"]["errorMsg"] ?></p>
        <?php endif; unset($_SESSION["signin"]["errorMsg"]); ?>
        <form action="processing/signin_processing.php" method="post">
            <label for="firstname">Prénom</label>
            <input id="firstname" type="text" autocomplete="given-name" name="firstname" required>
            <label for="lastname">NOM</label>
            <input id="lastname" type="text" autocomplete="family-name" autocapitalize="characters" name="lastname" required>
            <label for="email">Courriel</label>
            <input id="email" type="email" autocomplete="email" name="email" required>
            <label for="phone">Télephone</label>
            <select id="phone-code" name="phoneCode" autocomplete="tel-country-code">
                <?php foreach ($phoneCodes as $code) : 
                    $value = substr($code, 0, strpos($code, ' '));?>
                    <option value=<?=$value;?> <?= $value == "+33" ? "selected" : "";?>><?=$code?></option>
                <?php endforeach; ?>
            </select>
            <input id="phone" type="text" name="phone" autocomplete="tel-national" required>
            <label for="password1">Mot de passe</label>
            <input id="password1" type="password" autocomplete="new-password" name="password1" required>
            <label for="password2">Vérification du mot de passe</label>
            <input id="password2" type="password" name="password2" required>
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