<?php 
    include_once("include/config.php");
    include_once("view/generators/HeaderCreator.php");
    include_once("view/generators/UserFormCreator.php");
    include_once("view/generators/KeywordsFormCreator.php");
    include_once("view/generators/MicrosFormCreator.php");
    include_once("view/generators/GroupDetailsCreator.php");
    include_once("model/services/UserService.php");
    include_once("model/services/MicroscopesGroupService.php");

    if(!isUserSessionValid() || !$_SESSION["user"]["admin"]) 
        redirect("/index.php");

    $header = new HeaderCreator("Administration"); 

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
                echo "<h2>Fiches</h2>";
                echo "<div id='micro-group-forms'>";
                $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroup();
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
                    echo "<p>Il n'y a aucune fiche en attente pour l'instant</p>";
                else {
                    foreach($lockedGroups as $group)
                        (new GroupDetailsCreator($group, false))->create();
                }
                echo "<h2>Validées</h2>";
                if(sizeof($unlockedGroups) < 1)
                    echo "<p>Il n'y a aucune fiche validée pour l'instant</p>";
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
            case 'keywords':
                echo '<script src="/public/js/keywords_form.js" defer></script>';
                break;
            case 'micros':
                echo '<script src="/public/js/micros_form.js" defer></script>';
                break;
            case 'users':
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
            <li><a href="/admin.php?action=groups">Fiches</a></li>
            <li><a href="/admin.php?action=keywords">Mots-clés</a></li>
            <li><a href="/admin.php?action=micros">Microscopes</a></li>
            <li><a href="/admin.php?action=users">Utilisateurs</a></li>
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