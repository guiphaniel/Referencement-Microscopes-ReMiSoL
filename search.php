<?php
    include("model/services/MicroscopesGroupService.php");
    include("view/generators/GroupDetailsCreator.php");

    if(!isset($_GET["filters"]))
        $filters = [];
    else
        $filters = explode(" ", $_GET["filters"]);

    $groups = MicroscopesGroupService::getInstance()->findAllMicroscopesGroup(false, $filters);

    include_once("view/generators/HeaderCreator.php");
        $header = new HeaderCreator("Recherche", $_GET["filters"]??""); 
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
            foreach($groups as $group) {
                foreach($group->getMicroscopes() as $micro): 
                    $ctr = $micro->getController();
                    $model = $micro->getModel();
                    $brand = $model->getBrand();
                    $compagny = $brand->getCompagny();
                    $color = match ($micro->getType()) {
                        "LABO" => "blue-border",
                        "PLAT" => "red-border"
                    };
                    if($compagny->getName() == "Homemade")
                        $name = "Homemade - " . $ctr->getName();
                    else
                        $name = implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $ctr->getName()]);?>
                    <a class="tile <?=$color?>" href="/group-details.php?id=<?=$group->getId()?>">
                        <div class="picture"></div>
                        <h2><?=$name?><?=!empty($micro->getRate()) ? " - €" : ""?></h2>
                        <p><?=$group->getLab()->getAddress()->toString()?></p>
                        <?php
                            $access = $micro->getAccess();
                            if($access == "ACAD") : ?>
                                <p>Ouvert aux académiques</p>
                            <?php
                            elseif($access == "INDU") : ?>
                                <p>Ouvert aux industriels</p>
                        <?php elseif($access == "BOTH"): ?>
                            <p>Ouvert aux académiques et aux industriels</p>
                        <?php endif; ?>
                        <ul>
                            <?php
                                foreach ($micro->getKeywords() as $kw)
                                    $cats[$kw->getCat()->getName()][] = $kw->getTag();
                            
                                foreach($cats??[] as $cat => $tags): ?>
                                    <li><?= $cat; ?> : <?=implode(", ", $tags)?></li>
                                <?php endforeach; unset($cats);?>
                        </ul>
                    </a>    
                <?php endforeach;
            }
        ?>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
</html>