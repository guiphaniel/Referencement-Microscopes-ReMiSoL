<?php 
    include_once("view/generators/HeaderCreator.php");
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
                <label for="lab-address">Adresse</label>
                <input id="lab-address" type="text" name="labAddress" required>
            </fieldset>
            <fieldset id="microscopes">
                <legend>Vos microscopes</legend>
                <fieldset>
                    <legend>Contact</legend>
                    <label for="contact-name">Nom</label>
                    <input id="contact-name" type="text" name="contactName" required>
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
                    <label for="micro-brand-0">Marque</label>
                    <input id="micro-brand-0" type="text" name="microscopes[0][microBrand]" required>
                    <label for="micro-ref-0">Référence</label>
                    <input id="micro-ref-0" type="text" name="microscopes[0][microRef]" required>
                    <label for="micro-rate-0">Tarification</label>
                    <input id="micro-rate-0" type="text" name="microscopes[0][microRate]" required>
                    <label for="micro-desc-0">Description</label>
                    <textarea id="micro-desc-0" name="microscopes[0][microDesc]" cols="30" rows="10" required></textarea>
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