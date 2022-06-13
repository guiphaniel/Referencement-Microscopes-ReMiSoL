<?php
    include("model/services/MicroscopeService.php");
    include("model/services/LabService.php");
    include("view/creators/GroupDetailsCreator.php");

    if(empty($_GET["page"]) || $_GET["page"] < 0)
        $page = 0;
    else
        $page = $_GET["page"];

    if(empty($_GET["filters"]))
        $filters = [];
    else {
        $filters = array_filter(explode(" ", $_GET["filters"]));
    }

    $microscopeService = MicroscopeService::getInstance();
    $micros = $microscopeService->findAllMicroscopes(false, $filters, RESULT_PER_QUERY, RESULT_PER_QUERY * $page);
    $nbTotalMicros = $microscopeService->countAllMicroscopes(false, $filters);

    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");
    $header = new HeaderCreator("Recherche", str_replace("'", "&apos;", $_GET["filters"]??"")); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page affichant les microscopes correspondant à la recherche.">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Recherche</title>
</head>
<body>
    <?php $header->create(); ?>
    <main>
        <div class="infos">
            <?php if($nbTotalMicros == 0): ?>
                <p>Nous n'avons trouvé aucun microscope correspondant à votre recherche.</p>
            <?php elseif($nbTotalMicros == 1): ?>
                <p>1 microscope correspond à votre recherche.</p>
            <?php else: ?>
                <p><?=$nbTotalMicros?> microscopes correspondent à votre recherche.</p>
            <?php endif; ?>
        </div>
        <div class="tiles-wrapper">
            <?php
            foreach($micros as $groupId => $micros) {
                foreach($micros as $microId => $micro) {
                    $ctr = $micro->getController();
                    $model = $micro->getModel();
                    $brand = $model->getBrand();
                    $compagny = $brand->getCompagny();
                    $color = match ($micro->getType()) {
                        "LABO" => "lab-tile",
                        "PLAT" => "plat-tile"
                    };
                    if($compagny->getName() == "Homemade")
                        $name = "Homemade - " . $ctr->getName();
                    else
                        $name = implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $ctr->getName()]);
                        
                    $imgPath = MicroscopeService::getInstance()->getImgPathById($microId);?>
                    
                    <a class="tile <?=$color?>" href="/group-details.php?id=<?=$groupId?>&microId=<?=$microId?>">
                        <div class="img-wrapper">
                            <img src="<?=$imgPath?>" alt="Microscope <?=$name?>">
                        </div>
                        <div class="tile-content">
                            <h2><?=$name?><?=!empty($micro->getRate()) ? ' - <span class="euro">€</span>' : ""?></h2>
                            <p><?=LabService::getInstance()->findLabByGroupId($groupId)->getAddress()->toString()?></p>
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
                        </div>
                    </a>    
                <?php
                }
            }
            ?>
        </div>
        <?php if($nbTotalMicros > RESULT_PER_QUERY): ?>
        <nav id="page-nav">
            <ul>
                <?php $maxPage = ceil($nbTotalMicros / RESULT_PER_QUERY) - 1;
                if($page > 0): ?>
                    <li><a id="previous-page" class="bt" href="/search.php?filters=<?=$_GET["filters"]?>&page=<?=max(0, $page - 1)?>">< Précédent</a></li>
                <?php endif;?>
                <li><a href="/search.php?filters=<?=$_GET["filters"]?>&page=0" <?=$page == 0 ? 'class="current-page"' : ""?>><?=1?></a></li>
                <?php for($i = max(1, $page - 3); $i < min($page + 4, $maxPage) ; $i++): ?>
                    <li><a href="/search.php?filters=<?=$_GET["filters"]?>&page=<?=$i?>" <?=$page == $i ? 'class="current-page"' : ""?>><?=$i + 1?></a></li>
                <?php endfor; ?>
                <li><a href="/search.php?filters=<?=$_GET["filters"]?>&page=<?=$maxPage?>" <?=$page == $maxPage ? 'class="current-page"' : ""?>><?=$maxPage + 1?></a></li>
                <?php if($nbTotalMicros > ($page + 1) * RESULT_PER_QUERY): ?>
                    <li><a  id="next-page" class="bt" href="/search.php?filters=<?=$_GET["filters"]?>&page=<?=$page + 1?>">Suivant ></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>