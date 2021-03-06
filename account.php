<?php
    include_once("config/config.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    include_once("view/creators/UserFormCreator.php");
    include_once("view/creators/GroupDetailsCreator.php");
    include_once("model/services/MicroscopesGroupService.php");

    if(!isUserSessionValid()) 
        redirect("/index.php");

    $header = new HeaderCreator("Mon compte"); 

    function isActive($action) {
        if(empty($_GET["action"])) {
            if($action == 'groups')
                return 'active';
            else
                return;
        }
            
        if ($_GET["action"] == $action)
            return 'active';
    }

    function getMapping($action) {
        switch ($action) {
            case 'settings':
                echo "<h2>Paramètres</h2>";
                    (new UserFormCreator(UserService::getInstance()->findUserById($_SESSION["user"]["id"]), $_SESSION["user"]["admin"]))->create();
                break;
            default:
                echo "<h2>Mes fiches</h2>";
                echo "<div>";
                    $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroupByOwner($_SESSION["user"]["id"]);
                    $lockedGroups = [];
                    $unlockedGroups = [];
                    foreach ($groups as $group) {
                        if($group->isLocked())
                            $lockedGroups[] = $group;
                        else
                            $unlockedGroups[] = $group;
                    }
                    $nbLocked = sizeof($lockedGroups);
                    $nbUnlocked = sizeof($unlockedGroups);
                    echo "<h2>En attentes ($nbLocked)</h2>";
                    echo '<div class="group-details-wrapper">';
                        if(sizeof($lockedGroups) < 1)
                            echo "<p>Vous n'avez aucune fiche en attente pour l'instant. <a href=\"/form.php\">Créer une nouvelle fiche ?</a></p></p>";
                        else {
                            foreach($lockedGroups as $group)
                                (new GroupDetailsCreator($group, false))->create();
                        }
                    echo "</div>";
                    echo "<h2>Validées ($nbUnlocked)</h2>";
                    echo '<div class="group-details-wrapper">';
                        if(sizeof($unlockedGroups) < 1)
                            echo "<p>Vous n'avez aucune fiche validée pour l'instant.";
                        else {
                            foreach($unlockedGroups as $group)
                                (new GroupDetailsCreator($group, false))->create();
                        }
                    echo "</div>";
                echo "</div>";
            break;
        }
    }

    function loadJS($action) {
        echo '<script src="/public/js/undo.min.js" defer></script>';
        switch ($action) {
            case 'settings':
                echo '<script src="/public/js/user_form.min.js" defer></script>';
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
    <meta name="description" content="Compte de l'utilisateur, avec accès aux fiches et aux paramètres.">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <?php loadJS($_GET["action"]??""); ?>
    <title>Mon compte</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <div class="aside-wrapper">
            <aside>
                <nav>
                    <ul>
                        <li><a href="/account.php?action=groups" class="<?=isActive('groups')?>">Mes fiches</a></li>
                        <li><a href="/account.php?action=settings" class="<?=isActive('settings')?>">Paramètres</a></li>
                    </ul>
                </nav>
            </aside>
            <div class="aside-content">
                <?php getMapping($_GET["action"]??""); ?>
            </div>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>