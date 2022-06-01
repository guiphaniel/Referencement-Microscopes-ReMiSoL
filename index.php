<?php 
    include_once("model/services/ModelService.php");
    include_once("model/services/ControllerService.php");
    include_once("view/creators/HeaderCreator.php");
    $header = new HeaderCreator("Accueil"); 

    function createCheckboxes($objs, $type) {
        foreach($objs as $obj):
            $id = "$type-{$obj->getId()}";
            $name = $obj->getName(); ?>
            <div class="checkbox-group">
                <input type="checkbox" value="<?=$name?>" id="<?=$id?>">
                <svg class="checkmark">
                    <polyline points="1,5 6,9 14,1"></polyline>
                </svg>
                <label for="<?=$id?>"><?=$name?></label>
            </div>
    <?php endforeach;
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css"
    integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A=="
    crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="stylesheet" href="/public/css/MarkerCluster.css">
    <link rel="stylesheet" href="/public/css/MarkerCluster.Default.css">
    <script src="/public/js/leaflet.markercluster.js"></script>
    <title>Accueil</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <p>Bienvenue sur notre site !</p>
        <div class="aside-wrapper">
            <aside>
                <form id="map-filters">
                    <h2>Filtres</h2>
                    <input id="filters-reinit" type="reset" value="Réinitialiser">
                    <div class="filters-group">
                        <h3>Sociétés</h3>
                        <div>
                            <?php createCheckboxes(CompagnyService::getInstance()->findAllCompagnies(), "cmp"); ?>
                        </div>
                    </div>
                    <div class="filters-group">
                        <h3>Marques</h3>
                        <div>
                            <?php createCheckboxes(BrandService::getInstance()->findAllBrands(), "brand"); ?>
                        </div>
                    </div>
                    <div class="filters-group">
                        <h3>Modèles</h3>
                        <div>
                            <?php createCheckboxes(ModelService::getInstance()->findAllModels(), "model"); ?>
                        </div>
                    </div>
                    <div class="filters-group">
                        <h3>Électronique/Contrôleurs</h3>
                        <div>
                            <?php createCheckboxes(ControllerService::getInstance()->findAllControllers(), "ctr"); ?>             
                        </div>
                    </div>
                </form>
            </aside>
            <div id="map-wrapper">
                <div id="map"></div>
            </div>
        </div>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
<script src="public/js/map.js"></script>
</html>