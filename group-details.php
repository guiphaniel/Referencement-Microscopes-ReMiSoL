<?php 
    include_once("model/services/MicroscopesGroupService.php");
    include_once("view/generators/HeaderCreator.php");

    if(empty($_GET["id"])) {
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
    <link rel="stylesheet" href="public/css/style.css">
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
    integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
    crossorigin=""></script>
    <title>Détails</title>
</head>
<body>
    <?php 
        $header->create()
    ?>
    <main>
        <section>
            <h2><?= $group->getLab()->getName() . " (" . $group->getLab()->getCode() . ")"; ?></h2>
            <p><?= nl2br($group->getLab()->getAddress()->toString()); ?></p>
            <p>Site internet : <a href="<?= $group->getLab()->getWebsite(); ?>"><?= $group->getLab()->getWebsite(); ?></a></p>
            <div id="map" style="height: 400px;"></div>
        </section>
        <section>
            <h2>Référent·e·s</h2>
            <?php foreach ($group->getContacts() as $contact) : ?>
                <address>
                    <p><?= $contact->getFirstname() . ' ' . $contact->getLastname() . " (" . $contact->getRole() .")" ?></p>
                    <p>Email : <a href="mailto:<?= $contact->getEmail() ?>"><?= $contact->getEmail() ?></a></p>
                    <?php $phone = $contact->getPhoneCode() . $contact->getPhoneNum(); ?>
                    <p>Téléphone : <a href="tel:<?= $phone ?>"><?= $phone ?></a></p>
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
                    "LABO" => "laboratoire",
                    "PLAT" => "plateforme"
                };
                $name = implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $ctr->getName()]);
                ?>
                <section>
                    <h3><?= $name . " (" . $type . ")"; ?></h3>
                    <?php 
                        $microId = $micro->getId();
                        $path = glob(__DIR__ . "/public/img/micros/" . "$microId.*")[0]??false;

                        if($path):
                            $fileName = substr($path, strrpos($path, "/") + 1); 
                    ?>
                        <img class="micro-img" src="/public/img/micros/<?=$fileName?>" alt="Microscope <?=$name?>">
                    <?php endif; ?>
                    <p>Description : <?= $micro->getDesc(); ?></p>
                    <?php if(!empty($micro->getRate())) : ?>
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
                                <th scope="colgroup">Étiquettes</th>
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