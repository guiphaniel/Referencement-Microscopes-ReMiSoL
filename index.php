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
        <div class="infos">
            <p>Bienvenue sur notre notre site ! Que vous soyez chercheur, ingénieur, ou technicien, nous vous aiderons à référencer votre matériel à l'échelle nationale, et à trouver celui dont vous avez besoin.</p>
        </div>
        <div class="aside-wrapper">
            <aside>
                <form id="map-filters">
                    <div id="map-filters-header">
                        <h2>Filtres</h2>
                        <input id="filters-reset" type="reset" value="Réinitialiser">
                    </div>
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
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="def">            
                    <defs>
                        <path id="marker" d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z"/>
                    </defs>
                </svg>
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