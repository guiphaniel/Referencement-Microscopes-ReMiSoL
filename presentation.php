<?php 
    include_once("config/config.php");
    include_once("model/services/UserService.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");

    $header = new HeaderCreator("Présentation"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Page présentant le site web et le projet, ainsi que leurs objectifs.">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Présentation</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <h2>Historique</h2>
        <p>
            Le Réseau des Microscopies à Sonde Locale (<abbr title="Réseau des Microscopies à Sonde Locale">RéMiSoL</abbr>) est un réseau technologique de la Mission pour les Initiatives Transverses et Interdisciplinaires (<abbr title="Mission pour les Initiatives Transverses et Interdisciplinaires">MITI</abbr>) du <abbr title="Centre National de la Recherche Scientifique">CNRS</abbr> dont les membres sont implantés nationalement. L’objet du réseau est de fédérer les techniciens, ingénieurs et chercheurs ayant une expertise dans l’utilisation et/ou le développement de la microscopie champ proche. Les missions essentielles du réseau consistent à :
        </p>
        <ul class="decorated-list">
            <li>Suivre les évolutions thématiques et techniques de notre communauté et rester attentif à l'émergence de nouvelles applications autour du champ proche, afin d'accompagner et de faciliter les nouveaux développements instrumentaux</li>
            <li>Proposer des formations de base aux nouveaux utilisateurs des microscopes champ proche</li>
            <li>Favoriser la communication au sein de la communauté et entre les différentes communautés de métiers du <abbr title="Centre National de la Recherche Scientifique">CNRS</abbr> (Laboratoires, GDR, réseaux métiers, réseaux technologiques…)</li>
            <li>Initier et aider les échanges et les transferts de compétences techniques avec l'aide de la <abbr title="Mission pour les Initiatives Transverses et Interdisciplinaires">MITI</abbr>, le <abbr title="Centre National de la Recherche Scientifique">CNRS</abbr>, les délégations régionales et/ou les laboratoires</li>
            <li>Supporter des journées et ateliers concernant de nouveaux domaines d'utilisation ou des développements techniques autour de la microscopie à champ proche</li>
            <li>Soutenir financièrement et matériellement (prêt et/ou mutualisation de matériels) des projets scientifiques innovants</li>
        </ul>
        <p>
            Ce site de référencement des microscopes de la communauté s’inscrit parfaitement dans les missions de notre réseau. Il aidera les membres de la communauté à connaître leur environnement proche et les experts nationaux.
        </p>
        <h2>Objectifs</h2>
        <p>
            Ce site permet le référencement des microscopes champ proche présents sur le territoire. Les fiches ainsi créées permettront de donner une cartographie (sic) réaliste des plateformes et des microscope de laboratoire sur le territoire francophone.<br>
            L'accès aux informations est libre, mais un compte est nécessaire pour référencer son propre matériel.
        </p>
        <h2>Fonctionnement</h2>
        <p>
            Le site web est alimenté par la communauté. Chaque information est soumise au contrôle du comité, constitué par le bureau de <abbr title="Réseau des Microscopies à Sonde Locale">RéMiSoL</abbr>, puis rendue accessible à tous, par le biais de la carte présente sur la page d'accueil, la barre de recherche, et notre <a href="/api/v1/reference.php">API</a>.
        </p>
        <p>
            En cas de besoin, vous pouvez utiliser le <a href='/contact.php'>formulaire de contact</a> en pied de page.
        </p>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>


    