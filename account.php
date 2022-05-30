<?php
    include_once("include/config.php");
    include_once("view/generators/HeaderCreator.php");
    include_once("view/generators/UserFormCreator.php");
    include_once("view/generators/GroupDetailsCreator.php");
    include_once("model/services/MicroscopesGroupService.php");

    if(!isUserSessionValid()) 
        redirect("/index.php");

    $header = new HeaderCreator("Mon compte"); 

    function getMapping($action) {
        switch ($action) {
            case 'settings':
                echo "<h2>Paramètres</h2>";
                    (new UserFormCreator(UserService::getInstance()->findUserById($_SESSION["user"]["id"]), $_SESSION["user"]["admin"]))->create();
                break;
            default:
                echo "<h2>Fiches</h2>";
                echo "<div id='micro-group-forms'>";
                $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroupByOwner($_SESSION["user"]["id"]);
                $lockedGroups = [];
                $unlockedGroups = [];
                foreach ($groups as $group) {
                    if($group->isLocked())
                        $lockedGroups[] = $group;
                    else
                        $unlockedGroups[] = $group;
                }
                echo "<h2>En attentes</h2>";
                if(sizeof($lockedGroups) < 1)
                    echo "<p>Vous n'avez aucune fiche en attente pour l'instant</p>";
                else {
                    foreach($lockedGroups as $group)
                        (new GroupDetailsCreator($group, false))->create();
                }
                echo "<h2>Validées</h2>";
                if(sizeof($unlockedGroups) < 1)
                    echo "<p>Vous n'avez aucune fiche validée pour l'instant</p>";
                else {
                    foreach($unlockedGroups as $group)
                        (new GroupDetailsCreator($group, false))->create();
                }
                echo "</div>";
                break;
        }
    }

    function loadJS($action) {
        switch ($action) {
            case 'settings':
                echo '<script src="/public/js/user_form.js" defer></script>';
                break;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <?php loadJS($_GET["action"]??""); ?>
    <title>Administration</title>
</head>
<body>
    <?php $header->create() ?>
    <aside>
        <ul>
            <li><a href="/account.php?action=groups">Fiches</a></li>
            <li><a href="/account.php?action=settings">Paramètres</a></li>
        </ul>
    </aside>
    <main>
        <?php getMapping($_GET["action"]??""); ?>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>