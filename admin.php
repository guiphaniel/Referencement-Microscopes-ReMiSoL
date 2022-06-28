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
                if(empty($_GET["page"]) || $_GET["page"] < 0)
                    $page = 0;
                else
                    $page = $_GET["page"];
                $userService = UserService::getInstance();
                $nbTotalUsers = $userService->countAllUsers(); ?>
                <h2>Utilisateurs (<?=$nbTotalUsers?>)</h2>
                <div id='user-forms'>
                    <?php
                foreach($userService->findAllUsers(RESULT_PER_QUERY, $page * RESULT_PER_QUERY) as $user)
                    (new UserFormCreator($user, true))->create(); ?>
                </div>
                <?php if($nbTotalUsers > RESULT_PER_QUERY): ?>
                <nav id="page-nav">
                    <ul>
                        <?php $maxPage = ceil($nbTotalUsers / RESULT_PER_QUERY) - 1;
                        if($page > 0): ?>
                            <li><a id="previous-page" class="bt" href="/admin.php?action=users&page=<?=max(0, $page - 1)?>">< Précédent</a></li>
                        <?php endif;?>
                        <li><a href="/admin.php?action=users&page=0" <?=$page == 0 ? 'class="current-page"' : ""?>><?=1?></a></li>
                        <?php for($i = max(1, $page - 3); $i < min($page + 4, $maxPage) ; $i++): ?>
                            <li><a href="/admin.php?action=users&page=<?=$i?>" <?=$page == $i ? 'class="current-page"' : ""?>><?=$i + 1?></a></li>
                        <?php endfor; ?>
                        <li><a href="/admin.php?action=users&page=<?=$maxPage?>" <?=$page == $maxPage ? 'class="current-page"' : ""?>><?=$maxPage + 1?></a></li>
                        <?php if($nbTotalUsers > ($page + 1) * RESULT_PER_QUERY): ?>
                            <li><a  id="next-page" class="bt" href="/admin.php?action=users&page=<?=$page + 1?>">Suivant ></a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <?php endif; ?>
                <?php break;
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
                echo '<script src="/public/js/keywords_form.min.js" defer></script>';
                break;
            case 'micros':
                echo '<script src="/public/js/micros_form.min.js" defer></script>';
                break;
            case 'users':
                echo '<script src="/public/js/undo.min.js" defer></script>';
                echo '<script src="/public/js/user_form.min.js" defer></script>';
                break;
            default:
                echo '<script src="/public/js/undo.min.js" defer></script>';
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
    <meta name="description" content="Compte de l'administrateur, avec accès aux fiches, aux mots-clés, au matériel et aux utilisateurs.">
    <link rel="stylesheet" href="/public/css/style.min.css">
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