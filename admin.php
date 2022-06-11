<?php 
    include_once("config/config.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    include_once("view/creators/UserFormCreator.php");
    include_once("view/creators/KeywordsFormCreator.php");
    include_once("view/creators/MicrosFormCreator.php");
    include_once("view/creators/GroupDetailsCreator.php");
    include_once("model/services/UserService.php");
    include_once("model/services/MicroscopesGroupService.php");

    if(!isUserSessionValid() || !$_SESSION["user"]["admin"]) 
        redirect("/index.php");

    $header = new HeaderCreator("Administration"); 

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
            case 'keywords':
                echo "<h2>Mots-clés</h2>";
                    (new KeywordsFormCreator())->create();
                break;
            case 'micros':
                echo "<h2>Matériel</h2>";
                    (new MicrosFormCreator())->create();
                break;
            case 'users':
                echo "<h2>Utilisateurs</h2>";
                echo "<div id='user-forms'>";
                foreach(UserService::getInstance()->findAllUsers() as $user)
                    (new UserFormCreator($user, true))->create();
                echo "</div>";
                break;
            default:
                echo "<h2>Fiches en attente</h2>";
                echo "<div>";
                    $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroup();
                    $lockedGroups = [];
                    foreach ($groups as $group) {
                        if($group->isLocked())
                            $lockedGroups[] = $group;
                    }
                    $nbLocked = sizeof($lockedGroups);
                    if(sizeof($lockedGroups) < 1)
                        echo "<p>Il n'y a aucune fiche en attente pour l'instant</p>";
                    else {
                        foreach($lockedGroups as $group)
                            (new GroupDetailsCreator($group, false))->create();
                    }
                echo "</div>";
                break;
        }
    }

    function loadJS($action) {
        switch ($action) {
            case 'keywords':
                echo '<script src="/public/js/keywords_form.js" defer></script>';
                break;
            case 'micros':
                echo '<script src="/public/js/micros_form.js" defer></script>';
                break;
            case 'users':
                echo '<script src="/public/js/undo.js" defer></script>';
                echo '<script src="/public/js/user_form.js" defer></script>';
                break;
            default:
                echo '<script src="/public/js/undo.js" defer></script>';
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
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <?php loadJS($_GET["action"]??""); ?>
    <title>Administration</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <div class="aside-wrapper">
            <aside>
                <nav>
                    <ul>
                        <li><a href="/admin.php?action=groups" class="<?=isActive('groups')?>">Fiches</a></li>
                        <li><a href="/admin.php?action=keywords" class="<?=isActive('keywords')?>">Mots-clés</a></li>
                        <li><a href="/admin.php?action=micros" class="<?=isActive('micros')?>">Microscopes</a></li>
                        <li><a href="/admin.php?action=users" class="<?=isActive('users')?>">Utilisateurs</a></li>
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