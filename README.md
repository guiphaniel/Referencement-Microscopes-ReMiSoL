# Referencement de Microscopes - RéMiSoL
## Présentation
Le Réseau des Microscopies à Sonde Locale (RéMiSoL) est un réseau technologique de la Mission pour les Initiatives Transverses et Interdisciplinaires (MITI) du CNRS dont les membres sont implantés nationalement. L’objet du réseau est de fédérer les techniciens, ingénieurs et chercheurs ayant une expertise dans l’utilisation et/ou le développement de la microscopie champ proche. 

Ce site permet le référencement des microscopes champ proche présents sur le territoire et s’inscrit parfaitement dans les missions de notre réseau. Il aidera les membres de la communauté à connaître leur environnement proche et les experts nationaux.

Les fiches ainsi créées permettent de donner une cartographie réaliste des plateformes et des microscope de laboratoire sur le territoire francophone.
L'accès aux informations est libre, mais un compte est nécessaire pour référencer son propre matériel.

## Installation

Prérequis :
* Serveur exécutant PHP 8.1 (ou ultérieur)
* Serveur SMTP
* Base de données MySQL

Configuration :
Modifiez les informations du fichier [config/config.php](config/config.php) de manière adaptée.

Remplacez l'adresse présente dans le fichier [.htaccess](.htaccess) (ligne 4) par l'adresse de votre domaine.

## Licence

Ce site est protégé par la licence CECILL-C. Il doit rester open-source et sous cette licence et vous devez citer Guilhem RICHAUD.

## Développement

Si vous souhaitez apporter votre contribution au projet ou utiliser le code source comme base d'un autre projet, référez-vous à la licence. 

Afin de simplifier le développement en local, vous avez la possibilité d'utiliser une base de données SQLite : [model/database.db](model/database.db). Afin de l'utiliser modifiez le fichier [config/config.php](config/config.php) en conséquence :

        const MY_DBMS = DBMS::SQLite;

## Crédits

> Guilhem RICHAUD - Étudiant à l'IUT Lyon 1, spécialité Informatique, site de Bourg-en-Bresse
