<?php 
    include_once("../../config/config.php");
    include_once("../../view/creators/HeaderCreator.php");
    include_once("../../view/creators/FooterCreator.php");

    $header = new HeaderCreator("API"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/public/css/style.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>API</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <h2>Presentation</h2>
        <p>
            Afin de vous aider à créer votre propre application de consultation du matériel mis à disposition sur notre site, nous mettons à disposition notre <strong>API REST</strong>. <br>
            Celle-ci est accessible à tous, et à tout moment. 
        </p>
        <h2>Avertissement</h2>
        <p> 
            Cette API etant libre d'accès, nous vous prions d'en avoir une utilisation raisonnable, et d'éviter les requêtes trop fréquentes, qui risqueraient de surcharger notre site. <br>
            Pour toute question technique sur l'utilisation de l'API, ou pour tout problème rencontré, n'hésitez pas à ouvrir une "issue" sur notre <a href="https://github.com/guiphaniel/Referencement-Microscopes-ReMiSoL" target="_blank">GitHub</a>.
        </p>
        <h2>Utilisation</h2>
        <p>nb : les recherches son <strong>insensibles à la casse</strong></p>
        <div class="api-wrapper">
            <h3>/search</h3>
            <p>Retourne, parmis les groupes de microscopes présents sur le site, ceux comportant au moins une occurrence d'un des filtres fournis, parmis leur <strong>matériel</strong> (société, marque, modèle, élèctronique), leur <strong>description</strong>, leurs <strong>mots-clés</strong>, ou le <strong>nom de famille</strong> d'un·e de leurs référent·e·s.</p>
            <p>La recherche s'effectue donc avec un <strong>ou logique</strong> <code>|</code> entre les filtres</p>
            <h4>Paramètres</h4>
            <table class="api-params">
                <thead>
                    <tr>
                        <th scope="colgroup">Nom</th>
                        <th scope="colgroup">Type</th>
                        <th scope="colgroup">Requis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="rowgroup">filters</td>
                        <td scope="rowgroup">array(string) | string</td>
                        <td scope="rowgroup">non</td>
                    </tr>
                </tbody>
            </table>
            <h4>Exemples</h4>
            <ul class="examples-wrapper">
                <li><code>/api/v1/search.php</code></li>
                <li><code>/api/v1/search.php?filters=Bruker</code></li>
                <li><code>/api/v1/search.php?filters[]=Bruker&filters[]=basse temperature</code></li>
            </ul>
        </div>
        <div class="api-wrapper">
            <h3>/list_brands</h3>
            <p>Retourne toutes les marques, ou les marques d'une société donnée.</p>
            <h4>Paramètres</h4>
            <table class="api-params">
                <thead>
                    <tr>
                        <th scope="colgroup">Nom</th>
                        <th scope="colgroup">Type</th>
                        <th scope="colgroup">Requis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="rowgroup">compagny</td>
                        <td scope="rowgroup">string</td>
                        <td scope="rowgroup">non</td>
                    </tr>
                </tbody>
            </table>
            <h4>Exemples</h4>
            <ul class="examples-wrapper">
                <li><code>/api/v1/list_brands.php</code></li>
                <li><code>/api/v1/list_brands.php?compagny=Bruker</code></li>
            </ul>
        </div>
        <div class="api-wrapper">
            <h3>/list_models</h3>
            <p>Retourne tous les modèles de microscope, ou les modèles de microscope d'une marque donnée.</p>
            <h4>Paramètres</h4>
            <table class="api-params">
                <thead>
                    <tr>
                        <th scope="colgroup">Nom</th>
                        <th scope="colgroup">Type</th>
                        <th scope="colgroup">Requis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="rowgroup">brand</td>
                        <td scope="rowgroup">string</td>
                        <td scope="rowgroup">non</td>
                    </tr>
                </tbody>
            </table>
            <h4>Exemples</h4>
            <ul class="examples-wrapper">
                <li><code>/api/v1/list_models.php</code></li>
                <li><code>/api/v1/list_models.php?brand=JPK</code></li>
            </ul>
        </div>
        <div class="api-wrapper">
            <h3>/list_controllers</h3>
            <p>Retourne toutes les électroniques, ou les électroniques d'une marque donnée.</p>
            <h4>Paramètres</h4>
            <table class="api-params">
                <thead>
                    <tr>
                        <th scope="colgroup">Nom</th>
                        <th scope="colgroup">Type</th>
                        <th scope="colgroup">Requis</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="rowgroup">brand</td>
                        <td scope="rowgroup">string</td>
                        <td scope="rowgroup">non</td>
                    </tr>
                </tbody>
            </table>
            <h4>Exemples</h4>
            <ul class="examples-wrapper">
                <li><code>/api/v1/list_controllers.php</code></li>
                <li><code>/api/v1/list_controllers.php?brand=JPK</code></li>
            </ul>
        </div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>


    