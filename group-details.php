<?php 
    include_once("model/services/MicroscopesGroupService.php");
    include_once("view/generators/HeaderCreator.php");

    if(!isset($_GET["id"])) {
        header('location: /index.php');
        exit();
    }

    $group = MicroscopesGroupService::getInstance()->findMicroscopesGroupById($_GET["id"]); 
    if($group == null) {
        header('location: /index.php');
        exit();
    }

    $header = new HeaderCreator("Détails"); 
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
    <title>Accueil</title>
</head>
<body>
    <?php 
        $header->create()
    ?>
    <main>
        <section>
            <h2><?= $group->getLab()->getName(); ?></h2>
            <p><?= $group->getLab()->getAddress(); ?></p>
            <p>Site internet : <a href="<?= $group->getLab()->getWebsite(); ?>"><?= $group->getLab()->getWebsite(); ?></a></p>
            <div id="map" style="height: 400px;"></div>
        </section>
        <section>
            <h2>Référents</h2>
            <?php foreach ($group->getContacts() as $contact) : ?>
                <address>
                    <p><?= $contact->getFirstname() . ' ' . $contact->getLastname() . " (" . $contact->getRole() .")" ?></p>
                    <p>Email : <a href="mailto:<?= $contact->getEmail() ?>"><?= $contact->getEmail() ?></a></p>
                    <?php $phone = $contact->getPhone();
                        if(isset($phone)) : ?>
                            <p>Téléphone : <a href="tel:<?= $phone ?>"><?= $phone ?></a></p>
                    <?php endif; ?>
                </address>
            <?php endforeach ?>
        </section>
        <section>
            <h2>
                Microscopes
            </h2>
            <?php foreach ($group->getMicroscopes() as $micro) : 
                $ctr = $micro->getController();
                $model = $micro->getModel();
                $brand = $model->getBrand();
                $compagny = $brand->getCompagny();
                $type = match ($micro->getType()) {
                    "LABO" => "Laboratoire",
                    "PLAT" => "Plateforme"
                };
                ?>
                <section>
                    <h3><?= implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $ctr->getName()]) . " (" . $type . ")"; ?></h3>
                    <p>Description : <?= $micro->getDesc(); ?></p>
                    <?php if($micro->getRate() !== null) : ?>
                        <p>Tarification : <a href="<?= $micro->getRate(); ?>"><?= $micro->getRate(); ?></a></p>
                    <?php endif; ?>
                    <?php 
                    $access = $micro->getAccess();
                    if($access == "BOTH" || $access == "ACAD") : ?>
                        <p>Ouvert aux académiques</p>
                    <?php endif;
                    if($access == "BOTH" || $access == "INDU") : ?>
                        <p>Ouvert aux industriels</p>
                    <?php endif; ?>
                    <table>
                        <caption>Mots-clés</caption>
                        <thead>
                            <tr>
                                <th scope="colgroup">Catégories</th>
                                <th scope="colgroup">Mots-clés</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($micro->getKeywords() as $cat => $tags) : ?>
                                <tr>
                                    <th scope="rowgroup"><?= $cat; ?></th>
                                    <td><?= implode(", ", $tags); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </section>
            <?php endforeach ?>
        </section>
    </main>
    <footer>
        <address>
            <a href="mailto:xxx.xxx@xxx.fr">xxx.xxx@xxx.fr</a>
        </address>
    </footer>
</body>
<script src="public/js/map.js"></script>
</html>