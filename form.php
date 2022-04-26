<?php 
    include_once("view/generators/HeaderCreator.php");
    include_once("model/services/KeywordService.php");
    include_once("model/services/CompagnyService.php");
    include_once("utils/normalize_utf8_string.php");
    $header = new HeaderCreator("Formulaire"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css">
    <title>Formulaire</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <form action="processing/form_processing.php" method="post">
            <fieldset>
                <legend>Votre Laboratoire / Service</legend>
                <label for="lab-name">Nom</label>
                <input id="lab-name" type="text" name="labName" required>
                <label for="lab-address">Adresse postale</label>
                <input id="lab-address" type="text" name="labAddress" required>
            </fieldset>
            
            <fieldset id="contacts">
                <legend>Référent·e·s</legend>
                <fieldset id="contact-field-0">
                    <legend>Référent·e</legend>
                    <label for="contact-firstname-0">Prénom</label>
                    <input id="contact-firstname-0" type="text" name="contacts[0][firstname]" required>
                    <label for="contact-lastname-0">Nom</label>
                    <input id="contact-lastname-0" type="text" name="contacts[0][lastname]" required>
                    <label for="contact-email-0">Email</label>
                    <input id="contact-email-0" type="text" name="contacts[0][email]" required>
                </fieldset>
                <div id="add-contact" class="add-bt"></div>
            </fieldset>
            <fieldset id="coor">
                <legend>Coordonnées</legend>
                <label for="lat">Latitude</label>
                <input id="lat" type="number" name="lat" min="-90" max="90" step="0.00001" required>
                <label for="lon">Longitude</label>
                <input id="lon" type="number" name="lon" min="-180" max="180" step="0.00001" required>
            </fieldset>
            <fieldset id="microscopes">
                <legend>Vos microscopes</legend>
                <!-- Compagnies datalist -->
                <datalist id="micro-compagnies">
                <?php 
                    foreach (CompagnyService::getInstance()->getAllCompagnies() as $compagny): ?>
                        <option value="<?=$compagny->getName()?>">
                    <?php endforeach; ?>
                </datalist>
                <!-- Keywords datalists -->
                <?php 
                    $keyWordService = KeywordService::getInstance();
                    $cats = $keyWordService->getAllCategories();
                    foreach ($cats as $cat): 
                        echo "<!-- $cat datalist -->" ?>
                        <datalist id="cats-<?=HTMLNormalize($cat)?>">
                        <?php 
                            $tags = $keyWordService->getAllTags($cat);
                            foreach ($tags as $tag): ?>
                                <option value="<?=$tag?>">
                            <?php endforeach; ?>
                        </datalist>
                    <?php endforeach; ?>
                <fieldset id="micro-field-0">
                    <legend>Votre microscope</legend>
                    <label for="micro-compagny-0">Société</label>
                    <input id="micro-compagny-0" list="micro-compagnies" name="microscopes[0][compagny]" required>
                    <label for="micro-brand-0">Marque</label>
                    <input id="micro-brand-0" list="micro-brands-0" name="microscopes[0][brand]" required disabled>
                    <datalist id="micro-brands-0">
                    </datalist>
                    <label for="micro-model-0">Modèle</label>
                    <input id="micro-model-0" list="micro-models-0" name="microscopes[0][model]" required disabled>
                    <datalist id="micro-models-0">
                    </datalist>
                    <label for="micro-controller-0">Électronique - contrôleur</label>
                    <input id="micro-controller-0" list="micro-controllers-0" name="microscopes[0][controller]" required disabled>
                    <datalist id="micro-controllers-0">
                    </datalist>
                    <label for="micro-rate-0">Tarification (lien)</label>
                    <input id="micro-rate-0" type="text" name="microscopes[0][rate]" required>
                    <label for="micro-desc-0">Description</label>
                    <textarea id="micro-desc-0" name="microscopes[0][desc]" cols="30" rows="10" required></textarea>
                    <fieldset id="keywords">
                        <legend>Mots-clés</legend>
                        <?php 
                            $keyWordService = KeywordService::getInstance();
                            $cats = $keyWordService->getAllCategories();
                            foreach ($cats as $cat): 
                                $normCat = HTMLNormalize($cat)?>
                                <div>
                                    <label for="cat-<?=$normCat?>-0"><?=$cat?></label>
                                    <input id="cat-<?=$normCat?>-0" class="cat-input" list="cats-<?=$normCat?>">
                                </div>
                            <?php endforeach; ?>
                    </fieldset>
                </fieldset>
                <div id="add-micro" class="add-bt"></div>
            </fieldset>
            <input type="submit">
        </form>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
<script src="public/js/form.js"></script>
</html>