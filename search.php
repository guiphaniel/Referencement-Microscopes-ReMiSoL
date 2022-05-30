<?php
    include("model/services/MicroscopesGroupService.php");
    include("view/generators/GroupDetailsCreator.php");

    if(!isset($_GET["filters"]))
        $filers = [];
    else
        $filters = explode(" ", $_GET["filters"]);

    $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroup(false, $filters);

    include_once("view/generators/HeaderCreator.php");
        $header = new HeaderCreator("Recherche", $_GET["filters"]); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Recherche</title>
</head>
<body>
    <?php $header->create(); ?>
    <main>
        <?php
            foreach($groups as $group)
                (new GroupDetailsCreator($group, false))->create();
        ?>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>