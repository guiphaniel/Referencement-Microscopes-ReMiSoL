<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire</title>
</head>
<body>
    <header>
        <h1>Formulaire</h1>
    </header>
    <main>
        <form action="processing/form_processing.php" method="post">
            <fieldset>
                <legend>Informations générales</legend>
                <label for="lab-name">Nom du laboratoire</label>
                <input id="lab-name" type="text" name="labName">
                <label for="micro-ref">Référence du microscope</label>
                <input id="micro-ref" type="text" name="microRef">
                <label for="description">Description</label>
                <textarea id="description" name="description" cols="30" rows="10"></textarea>
            </fieldset>
            <fieldset>
                <legend>Coordonnées</legend>
                <label for="lat">Latitude</label>
                <input id="lat" type="text" name="lat">
                <label for="lon">Longitude</label>
                <input id="lon" type="text" name="lon">
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