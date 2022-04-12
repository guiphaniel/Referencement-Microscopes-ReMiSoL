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
    <title>Formulaire</title>
</head>
<body>
    <?php $header->create() ?>
    <main>
        <form action="processing/form_processing.php" method="post">
            <fieldset>
                <legend>Informations générales</legend>
                <label for="lab-name">Nom du laboratoire</label>
                <input id="lab-name" type="text" name="labName" required>
                <label for="micro-ref">Référence du microscope</label>
                <input id="micro-ref" type="text" name="microRef" required>
                <label for="desc">Description</label>
                <textarea id="desc" name="desc" cols="30" rows="10" required></textarea>
            </fieldset>
            <fieldset>
                <legend>Coordonnées</legend>
                <label for="lat">Latitude</label>
                <input id="lat" type="number" name="lat" min="-90" max="90" step="0.00001" required>
                <label for="lon">Longitude</label>
                <input id="lon" type="number" name="lon" min="-180" max="180" step="0.00001" required>
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
</html>