<?php 
    include_once("view/generators/HeaderCreator.php");
    include_once("model/services/KeywordService.php");
    include_once("model/services/CompagnyService.php");
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
                <legend>Votre Laboratoire</legend>
                <label for="lab-name">Nom</label>
                <input id="lab-name" type="text" name="labName" required>
                <label for="lab-address">Adresse postale</label>
                <input id="lab-address" type="text" name="labAddress" required>
            </fieldset>
            <fieldset id="microscopes">
                <legend>Vos microscopes</legend>
                <!-- Here is the datalist for compagnies -->
                <datalist id="compagnies">
                <?php 
                echo var_dump(CompagnyService::getInstance()->getAllCompagnies());
                    foreach (CompagnyService::getInstance()->getAllCompagnies() as $compagny): ?>
                        <option value="<?=$compagny->getName()?>">
                    <?php endforeach; ?>
                </datalist>
                <fieldset>
                    <legend>Référent·e</legend>
                    <label for="contact-firstname">Prénom</label>
                    <input id="contact-firstname" type="text" name="contactFirstname" required>
                    <label for="contact-lastname">Nom</label>
                    <input id="contact-lastname" type="text" name="contactLastname" required>
                    <label for="contact-email">Email</label>
                    <input id="contact-email" type="text" name="contactEmail" required>
                </fieldset>
                <fieldset id="coor">
                    <legend>Coordonnées</legend>
                    <label for="lat">Latitude</label>
                    <input id="lat" type="number" name="lat" min="-90" max="90" step="0.00001" required>
                    <label for="lon">Longitude</label>
                    <input id="lon" type="number" name="lon" min="-180" max="180" step="0.00001" required>
                </fieldset>
                <fieldset id="micro-field-0">
                    <legend>Votre microscope</legend>
                    <label for="micro-compagny-0">Société</label>
                    <!-- The datalist for compagnies is at the beginnnig of the microscopes fieldset -->
                    <input id="micro-compagny-0" list="compagnies" name="microscopes[0][compagny]" required>
                    <label for="micro-brand-0">Marque</label>
                    <input id="micro-brand-0" type="text" name="microscopes[0][brand]" required>
                    <label for="micro-model-0">Modèle</label>
                    <input id="micro-model-0" type="text" name="microscopes[0][model]" required>
                    <label for="micro-controller-0">Électronique - contrôleur</label>
                    <input id="micro-controller-0" type="text" name="microscopes[0][controller]" required>
                    <label for="micro-rate-0">Tarification (lien)</label>
                    <input id="micro-rate-0" type="text" name="microscopes[0][rate]" required>
                    <label for="micro-desc-0">Description</label>
                    <textarea id="micro-desc-0" name="microscopes[0][desc]" cols="30" rows="10" required></textarea>
                    <fieldset id="keywords">
                    <legend>Mots-clés</legend>
                    <?php 
                        $keyWordService = KeywordService::getInstance();
                        $cats = $keyWordService->getAllCategories();
                        foreach ($cats as $cat): ?>
                            <label for="cat-<?=$cat?>"><?=$cat?></label>
                            <input id="cat-<?=$cat?>" list="cats-<?=$cat?>" name="microscopes[0][keywords][<?=$cat?>]">
                            <datalist id="cats-<?=$cat?>">
                            <?php 
                                $tags = $keyWordService->getAllTags($cat);
                                foreach ($tags as $tag): ?>
                                    <option value="<?=$tag?>">
                                <?php endforeach; ?>
                            </datalist>
                        <?php endforeach; ?>
                    </fieldset>
                </fieldset>
                <div id="add-micro"></div>
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