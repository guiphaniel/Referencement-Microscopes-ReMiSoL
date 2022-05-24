<?php 
    include_once("include/config.php");
    include_once("view/generators/HeaderCreator.php");
    include_once("view/generators/GroupFormCreator.php");
    include_once("model/services/KeywordService.php");
    include_once("model/services/CompagnyService.php");
    include_once("utils/normalize_utf8_string.php");

    $labTypes = ["UPR", "UMR", "IRL", "UAR", "FR", "EMR"];
    $countries = ["Belgique", "France", "Suisse"]; // Belgium, France, Switzerland
    $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

    if(!isUserSessionValid()) 
        redirect("/index.php");

    $header = new HeaderCreator("Formulaire"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Formulaire</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <?php (new GroupFormCreator())->create(); ?>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
    <script src="public/js/form.js"></script>
</body>
</html>