<?php 
    include_once("include/config.php");
    include_once("view/generators/HeaderCreator.php");
    include_once("model/services/KeywordService.php");
    include_once("model/services/CompagnyService.php");
    include_once("utils/normalize_utf8_string.php");

    $labTypes = ["UPR", "UMR", "IRL", "UAR", "FR", "EMR"];
    $countries = ["Belgique", "France", "Suisse"]; // Belgium, France, Switzerland
    $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

    if(!isUserSessionValid()) 
        redirect("/index.php");

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
        <?php if(isset($_SESSION["microForm"]["errorMsg"])) : ?>
            <p id="error-msg"><?= $_SESSION["microForm"]["errorMsg"] ?></p>
        <?php endif; unset($_SESSION["microForm"]["errorMsg"]); ?>
        <form action="processing/form_processing.php" method="post" enctype='multipart/form-data'>
            <fieldset>
                <legend>Votre laboratoire / service</legend>
                <address>
                    <label for="lab-name">Nom du laboratoire</label>
                    <input id="lab-name" type="text" name="lab[name]" autocomplete="organization" required>
                    <label for="lab-code">Code</label>
                    <select name="lab[type]" id="lab-type">
                    <?php foreach ($labTypes as $labType) : ?>
                        <option value=<?=$labType;?>><?=$labType?></option>
                    <?php endforeach; ?>
                    </select>
                    <input id="lab-code" type="number" name="lab[code]" min="10" max="9999" required>
                    <label for="lab-address-school">Université / École</label>
                    <input id="lab-address-school" type="text" name="lab[address][school]">
                    <label for="lab-address-street">Adresse</label>
                    <input id="lab-address-street" type="text" name="lab[address][street]" autocomplete="address-line1" required>
                    <label for="lab-address-zip">Code postal</label>
                    <input id="lab-address-zip" type="text" name="lab[address][zipCode]" autocomplete="postal-code" required>
                    <label for="lab-address-city">Ville</label>
                    <input id="lab-address-city" type="text" name="lab[address][city]" autocomplete="address-level2" required>
                    <label for="lab-address-country">Pays</label>
                    <select name="lab[address][country]" id="lab-address-country" autocomplete="country">
                    <?php foreach ($countries as $country) : ?>
                        <option value=<?=$country;?> <?= $country == "France" ? "selected" : "";?>><?=$country?></option>
                    <?php endforeach; ?>
                    </select>
                    <label for="lab-website">Site web</label>
                    <input id="lab-website" type="url" name="lab[website]" autocomplete="url" required>
                </address>
            </fieldset>   
            <fieldset id="contacts">
                <legend>Référent·e·s</legend>
                <fieldset id="contact-field-0" class="contact-field">
                    <legend>Référent·e n°1</legend>
                    <address>
                        <label for="contact-firstname-0">Prénom</label>
                        <input id="contact-firstname-0" type="text" name="contacts[0][firstname]" autocomplete="given-name" required>
                        <label for="contact-lastname-0">Nom</label>
                        <input id="contact-lastname-0" type="text" name="contacts[0][lastname]" autocomplete="family-name" required>
                        <label for="contact-role-0">Titre</label>
                        <input id="contact-role-0" type="text" name="contacts[0][role]" autocomplete="organization-title" required>
                        <label for="contact-email-0">Email</label>
                        <input id="contact-email-0" type="text" name="contacts[0][email]" autocomplete="email" required>
                        <label for="contact-phone-0">Téléphone</label>
                        <select name="contacts[0][phoneCode]" id="contact-phone-code-0" autocomplete="tel-country-code">
                        <?php foreach ($phoneCodes as $code) : 
                            $value = substr($code, 0, strpos($code, ' '));?>
                            <option value=<?=$value;?> <?= $value == "+33" ? "selected" : "";?>><?=$code?></option>
                        <?php endforeach; ?>
                        </select>
                        <input id="contact-phone-0" type="text" name="contacts[0][phone]" autocomplete="tel-national" required>
                    </address>
                </fieldset>
                <div id="add-contact" class="add-bt"></div>
            </fieldset>
            <fieldset id="coor">
                <legend>Coordonnées</legend>
                <label for="lat">Latitude</label>
                <input id="lat" type="number" name="coor[lat]" min="41" max="52" step="0.00001" required>
                <label for="lon">Longitude</label>
                <input id="lon" type="number" name="coor[lon]" min="-6" max="11" step="0.00001" required>
            </fieldset>
            <fieldset id="micros">
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
                <fieldset id="micro-field-0" class="micro-field">
                    <legend>Microscope n°1</legend>
                    <label for="micro-compagny-0">Société</label>
                    <input id="micro-compagny-0" list="micro-compagnies" name="micros[0][compagny]" required>
                    <label for="micro-brand-0">Marque</label>
                    <input id="micro-brand-0" list="micro-brands-0" name="micros[0][brand]" required disabled>
                    <datalist id="micro-brands-0">
                    </datalist>
                    <label for="micro-model-0">Modèle</label>
                    <input id="micro-model-0" list="micro-models-0" name="micros[0][model]" required disabled>
                    <datalist id="micro-models-0">
                    </datalist>
                    <label for="micro-controller-0">Électronique - contrôleur</label>
                    <input id="micro-controller-0" list="micro-controllers-0" name="micros[0][controller]" required disabled>
                    <datalist id="micro-controllers-0">
                    </datalist>
                    <label for="micro-type-0">Type</label>
                    <select id="micro-type-0" name="micros[0][type]">
                        <option value="LABO">Laboratoire</option>
                        <option value="PLAT">Plateforme</option>
                    </select>
                    <label for="micro-rate-0">Tarification (le cas échéant. Lien internet)</label>
                    <input id="micro-rate-0" type="url" name="micros[0][rate]" autocomplete="url">
                    <label for="micro-access-0">Ouvert aux</label>
                    <select name="micros[0][access]" id="micro-access-0">
                        <option value="ACAD">Académiques</option>
                        <option value="INDU">Industriels</option>
                        <option value="BOTH">Académiques et Industriels</option>
                    </select>
                    <label for="micro-desc-0">Description</label>
                    <textarea id="micro-desc-0" name="micros[0][desc]" cols="30" rows="10" required></textarea>
                    <label for="micro-img-0">Photo</label>
                    <input id="micro-img-0" name="imgs[]" type="file" accept="image/png, image/jpeg, image/webp">
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
    <script src="public/js/form.js"></script>
</body>
</html>