<?php 
    include_once("config/config.php");
    include_once("model/services/UserService.php");
    include_once("view/creators/HeaderCreator.php");
    include_once("view/creators/FooterCreator.php");

    $header = new HeaderCreator("Mentions légales"); 
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Mentions légales du site avec le contrat de licence de logiciel libre cecill-c">
    <link rel="stylesheet" href="/public/css/style.min.css">
    <link rel="preload" as="font" href="/public/fonts/OpenSans-ExtraBold.woff2" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" as="font" href="/public/fonts/MontSerrat.woff2" type="font/woff2" crossorigin="anonymous">
    <title>Mentions légales</title>
</head>
<body>
    <?php 
        $header->create();
    ?>
    <main>
        <h2>Publication</h2>
        <p>
            David Albertini <br>
            Mission pour les Initiatives Transverses et Interdisciplinaires (<abbr title="Mission pour les Initiatives Transverses et Interdisciplinaires">MITI</abbr> <abbr title="Centre National de la Recherche Scientifique">CNRS</abbr>) <br>
            Réseau des Microscopies à Sonde Locale (<abbr title="Réseau des Microscopies à Sonde Locale">RéMiSoL</abbr>)

        </p>
        <h2>Développement</h2>
        <address class="padding-bottom-1">
            Guilhem RICHAUD <br>
            Université Claude Bernard – Lyon 1 <br>
            Institut Universitaire de Technologie <br>
            Département Informatique <br>
            71 rue Peter Fink – 01000 BOURG-EN-BRESSE <br>
        </address>
        <h2>hébergement</h2>
        <address class="padding-bottom-1">
            IONOS SARL <br>
            7, place de la Gare, BP 70109, 57200 Sarreguemines Cedex <br>
            431 303 775 RCS Sarreguemines <br>
            tél. : <a href="tel:0970 808 911">0970 808 911</a>, courriel : <a href="mailto:info@IONOS.fr">info@IONOS.fr</a>
        </address>
        <h2>Données personnelles</h2>
        <p>
            En tant que visiteur, aucune donnée vous concernant n'est collectée. <br>
            En tant qu'utilisateur, seules les informations renseignées à l'inscription sont stockées, de manière sécurisée, sur nos serveurs. Vos informations ne sont traitées sous aucune forme que ce soit.
        </p>
        <p>
            Ce site ne dépose pas de cookies sur votre ordinateur / tablette / smartphone.
        </p>
        <h2>Droits d'auteur</h2>
        <p>
            Ce site est protégé par la licence CECILL-C. Il doit rester open-source et sous cette licence et vous devez citer Guilhem RICHAUD.
        </p>
        <p>
            Le code source du site est accessible sur <a href="https://github.com/guiphaniel/Referencement-Microscopes-ReMiSoL">GitHub</a>
        </p>
        <h2 class="title">CONTRAT DE LICENCE DE LOGICIEL LIBRE CeCILL-C</h2>
        <div class="notice">
        <h3>Avertissement</h3>

        <p>Ce
        contrat est une licence de logiciel libre issue d'une
        concertation entre ses auteurs afin que le respect de deux grands
        principes préside à sa rédaction:</p>
        <ul class="decorated-list">
        <li>
        d'une part, le respect des principes de diffusion des logiciels libres:
        accès au code  source, droits étendus conférés
        aux utilisateurs,</li>
        <li>
        d'autre
        part, la désignation d'un droit applicable, le droit français, auquel
        elle est conforme, tant au regard
        du droit de la responsabilité civile que du droit de la
        propriété intellectuelle et de la protection qu'il
        offre aux auteurs et titulaires des droits patrimoniaux sur un
        logiciel.</li>
        </ul>


        <p>Les
        auteurs de la licence 


        CeCILL-C<sup><a href="#footnote1">1</a></sup>
        sont:</p>

        <p>Commissariat
        à l'Energie Atomique - CEA, établissement
        public de recherche à caractère scientifique, technique et industriel, dont
        le siège est situé 25 rue Leblanc, immeuble Le Ponant D, 75015 Paris.</p>
        <p>Centre
        National de la Recherche Scientifique - CNRS,
        établissement public à caractère scientifique et
        technologique, dont le siège est situé 3 rue
        Michel-Ange, 75794 Paris cedex 16.</p>

        <p>Institut
        National de Recherche en Informatique et en Automatique - INRIA,
        établissement public à caractère scientifique et
        technologique, dont le siège est situé Domaine de
        Voluceau, Rocquencourt, BP 105, 78153 Le Chesnay cedex.</p>

        </div>
        <div class="preamble">
        <h3>Préambule</h3>






        <p>
        Ce contrat est une licence de logiciel libre dont l'objectif est de
        conférer aux utilisateurs la liberté de modifier et de réutiliser
        le logiciel régi par cette licence.
        </p>
        <p>
        L'exercice
        de cette liberté est assorti d'une obligation de remettre à la disposition de
        la communauté les modifications apportées au code source du logiciel afin de
        contribuer à son  évolution.
        </p>


        <p>L'accessibilité
        au code source et les droits de copie, de modification et de
        redistribution qui 
        découlent de ce contrat  ont 
        pour contrepartie de n'offrir aux utilisateurs qu'une garantie limitée
        et de ne faire peser sur l'auteur du logiciel, le titulaire des
        droits patrimoniaux et les concédants successifs qu'une
        responsabilité restreinte.
        </p>

        <p>A
        cet égard l'attention de l'utilisateur est attirée
        sur les risques associés au chargement, à
        l'utilisation, à la modification et/ou au développement
        et à la reproduction du logiciel par l'utilisateur étant
        donné sa spécificité de logiciel libre, qui peut
        le rendre complexe à manipuler et qui le réserve donc à
        des développeurs ou des professionnels avertis
        possédant des connaissances informatiques approfondies. Les utilisateurs sont
        donc invités à charger et tester l'adéquation
        du logiciel à leurs besoins dans des conditions permettant
        d'assurer la sécurité de leurs systèmes et/ou de leurs données et,
        plus généralement, à l'utiliser et l'exploiter dans les mêmes conditions de
        sécurité. Ce contrat peut être reproduit et diffusé librement, sous réserve
        de le conserver en l'état, sans ajout ni suppression de
        clauses.
        </p>
        <p>Ce
        contrat est susceptible de s'appliquer à tout logiciel
        dont le titulaire des droits patrimoniaux décide de soumettre
        l'exploitation aux dispositions qu'il  contient.</p>

        </div>
        <div class="article">

            <h3> Article 1  - DEFINITIONS</h3>

            <p>Dans
            ce contrat, les termes suivants, lorsqu'ils seront écrits
            avec une lettre capitale, auront la signification suivante:</p>

            <p class="definition"><span class="definition">Contrat</span>:
                
            désigne le présent contrat de licence, ses
                éventuelles versions postérieures  et annexes.
                
                </p>

                <p class="definition"><span class="definition">Logiciel</span>:
                
            désigne le logiciel sous sa forme de Code Objet et/ou de Code
            Source et le cas échéant sa documentation, dans leur
            état au moment de l'acceptation du Contrat par le
            Licencié.
                
                </p>

            <p class="definition"><span class="definition">Logiciel Initial</span>:
                
                désigne  le Logiciel sous sa forme de
            Code Source et éventuellement
                de Code Objet et le cas échéant sa
            documentation, dans leur état au moment de leur première
            diffusion sous les termes du Contrat.
                
                </p>

                

                
            <p class="definition"><span class="definition">Logiciel Modifié</span>:
                
                désigne le Logiciel modifié par au moins une Contribution Intégrée.
                </p>
                

            <p class="definition"><span class="definition">Code Source</span>:
                
                désigne l'ensemble des
            instructions et des lignes de programme du Logiciel et auquel
            l'accès est nécessaire en vue de modifier le
            Logiciel.
                
                </p>

            <p class="definition"><span class="definition">Code Objet</span>:
                
                désigne les fichiers binaires issus de la compilation du 
                Code Source.
                
                </p>

            <p class="definition"><span class="definition">Titulaire</span>:
                
                désigne le ou les détenteurs des droits
                patrimoniaux d'auteur sur le Logiciel Initial.
                
                </p>

            <p class="definition"><span class="definition">Licencié</span>:
                
            désigne le ou les utilisateurs du Logiciel
                ayant accepté 
            le Contrat.
                
                </p>

            <p class="definition"><span class="definition">Contributeur</span>:
                
                désigne le Licencié auteur d'au moins une 
                
                Contribution Intégrée.
                </p>

            <p class="definition"><span class="definition">Concédant</span>:
                
            désigne le Titulaire ou toute personne physique ou morale
            distribuant le Logiciel sous le Contrat.
                
                </p>

                

                
            <p class="definition"><span class="definition">Contribution Intégrée</span>:
                
            désigne l'ensemble des modifications, corrections,
            traductions, adaptations et/ou nouvelles fonctionnalités
            intégrées dans le Code Source par tout Contributeur.
                
                </p>
                

                
                <p class="definition"><span class="definition">Module Lié</span>:
                
                désigne un ensemble de fichiers sources y compris leur documentation
                qui, sans modification du Code Source, permet de réaliser des
                fonctionnalités ou services supplémentaires à ceux fournis par le
                Logiciel. 
                
                </p>

                <p class="definition"><span class="definition">Logiciel Dérivé</span>:
                
                désigne toute combinaison du Logiciel,
                modifié ou non, et d'un Module Lié.
                
                </p>
                
            


                

            <p class="definition"><span class="definition">Parties</span>:
                désigne collectivement le Licencié et le Concédant.
                </p>

            <p>Ces termes s'entendent au singulier comme au pluriel.</p>

        </div>
        <div class="article">

            <h3> Article 2  - OBJET</h3>

            <p>Le
            Contrat a pour objet la concession par le Concédant au
            Licencié d'une licence non exclusive, cessible
            et mondiale du Logiciel telle que définie ci-après à
            l'article <a href="#etendue">5</a> pour toute la durée de protection 
                des droits portant sur ce Logiciel. 
            </p>

        </div>
        <div class="article">

                <h3> Article 3  - ACCEPTATION</h3>

                <div class="clause">
            <p><a name="acceptation-acquise"></a>3.1 
            L'acceptation
            par le Licencié des termes du Contrat est réputée
            acquise du fait du premier des faits suivants:
            </p>
            <ul class="decorated-list">
            <li> (i)
            le chargement du Logiciel par tout moyen notamment par
            téléchargement à partir d'un serveur
            distant ou par chargement à partir d'un support
            physique;</li>
            <li>
            (ii)
            le premier exercice par le Licencié de l'un quelconque
            des droits concédés par le Contrat.</li>
                </ul>
                </div>

                <div class="clause">
            <p>3.2  Un
            exemplaire du Contrat, contenant notamment un avertissement relatif
            aux spécificités du Logiciel, à la restriction
            de garantie et à la limitation à un usage par des
            utilisateurs expérimentés a été mis à
            disposition du Licencié préalablement à son
            acceptation telle que définie à l'article 
                <a href="#acceptation-acquise">3.1</a> ci
            dessus  et le Licencié reconnaît en avoir pris
            connaissance.</p>
                </div>

        </div>
        <div class="article">

        <h3> Article 4  - ENTREE EN VIGUEUR ET DUREE</h3>

                <div class="clause">
                <h4>
        4.1 ENTREE EN VIGUEUR</h4>

            <p>Le
            Contrat entre en vigueur à la date de son acceptation par le
            Licencié telle que définie en <a href="#acceptation-acquise">3.1</a>.</p>

                </div>

                <div class="clause">
            <h4>
        <a name="duree"></a>4.2 DUREE</h4>

            <p>Le
            Contrat produira ses effets pendant toute la durée légale
            de protection des droits patrimoniaux portant sur le Logiciel.</p>

                </div>

        </div>
        <div class="article">

                <h3>
        <a name="etendue"></a> Article 5  - ETENDUE DES DROITS CONCEDES</h3>

            <p>Le
            Concédant concède au Licencié, qui accepte, les
            droits suivants sur le Logiciel pour toutes destinations et pour la
            durée du Contrat dans les conditions ci-après
            détaillées. 
            </p>
            <p>
                Par ailleurs, 
                si le Concédant détient ou venait à détenir un ou plusieurs
                brevets d'invention protégeant tout ou partie des fonctionnalités 
                du Logiciel ou de ses composants, il s'engage à ne pas
                opposer les éventuels droits conférés par ces brevets aux Licenciés 
                successifs qui utiliseraient, exploiteraient ou modifieraient le
                Logiciel. En cas de cession de ces brevets, le 
                Concédant s'engage à faire reprendre les obligations du présent alinéa
                aux cessionnaires.
                </p>

                <div class="clause">
                <h4>
        5.1 DROIT D'UTILISATION</h4>

            <p>Le
            Licencié est autorisé à utiliser le Logiciel,
            sans restriction quant aux domaines d'application, étant
            ci-après précisé que cela comporte:</p>
            <ol class="decorated-list">
            <li><p>la
            reproduction permanente ou provisoire du Logiciel en tout ou partie
            par tout moyen et sous toute forme. 
            </p></li>
            <li>
        <p>le
            chargement, l'affichage, l'exécution, ou le
            stockage du Logiciel sur tout support.</p>
                </li>
            <li>
        <p>la
            possibilité d'en observer, d'en étudier,
            ou d'en tester le fonctionnement afin de déterminer
            les idées et principes qui sont à la base de
            n'importe quel élément de ce Logiciel; et
            ceci, lorsque le Licencié effectue toute opération de
            chargement, d'affichage, d'exécution, de
            transmission ou de stockage du Logiciel qu'il est en droit
            d'effectuer en vertu du Contrat.</p>
                </li>
            </ol>

                </div>

                

                
                <div class="clause">
                <h4>
        5.2 DROIT DE MODIFICATION</h4>

                <p>
                Le droit de modification comporte le droit de
            traduire, d'adapter, d'arranger ou d'apporter
            toute autre modification 
                au Logiciel et le droit de reproduire le
            logiciel en résultant. Il comprend en particulier le droit de
                créer un Logiciel Dérivé.
                </p>

            <p>Le
            Licencié est autorisé à apporter toute
            modification au Logiciel sous réserve de mentionner, de façon
            explicite, son nom en tant qu'auteur de cette modification et
            la date de création de celle-ci.</p>

                </div>
                

                <div class="clause">
            <h4>
        5.3 DROIT
            DE DISTRIBUTION</h4>

            <p>Le
            droit de distribution 
                comporte notamment le droit de diffuser, de
            transmettre et de  communiquer le Logiciel au public sur tout
            support et par tout moyen ainsi que le droit de mettre sur le marché
            à titre onéreux ou gratuit, un ou des exemplaires du
            Logiciel par tout procédé.</p>
            <p>Le
            Licencié est autorisé à 
                distribuer des copies
            du Logiciel, modifié ou non, à des tiers dans les
            conditions ci-après détaillées.</p>

                <div class="subclause">
                <h5>
        5.3.1 DISTRIBUTION
            DU LOGICIEL SANS MODIFICATION</h5>

            <p>Le
            Licencié est autorisé à 
                distribuer des copies
            conformes du Logiciel, sous forme de Code Source ou de Code Objet,
                à condition que cette distribution respecte les
                dispositions du Contrat dans leur totalité et soit accompagnée:</p>
            <ol class="decorated-list">
            <li><p>d'un
            exemplaire du Contrat,</p></li>
            <li><p>d'un
                avertissement relatif à la restriction de garantie et de
            responsabilité du Concédant telle que prévue
            aux articles <a href="#responsabilite">8</a> et 
                <a href="#garantie">9</a>,</p></li>
            </ol>
            <p>et
            que, dans le cas où seul le Code Objet du Logiciel est
            redistribué, le Licencié permette un accès effectif
                au Code Source complet du Logiciel
            pendant au moins toute la durée
                de sa distribution du Logiciel, étant
            entendu que le coût additionnel d'acquisition du Code
            Source ne devra pas excéder le simple coût de transfert
            des données.</p>
        
                </div>

                <div class="subclause">
            <h5>
        <a name="distrib-modif"></a>5.3.2 DISTRIBUTION DU LOGICIEL MODIFIE</h5>

                

                

                
                <p>
                Lorsque le Licencié apporte une Contribution Intégrée au Logiciel, 
                les conditions de distribution du Logiciel Modifié 
                en résultant sont alors soumises à l'intégralité des
                dispositions du Contrat.
                </p>
                <p>
                Le Licencié est autorisé à distribuer le Logiciel Modifié sous forme
                de code source ou de code objet, à condition que cette distribution
                respecte les dispositions du Contrat dans leur totalité et soit
            accompagnée: 
            </p>
            <ol class="decorated-list">
            <li><p>d'un
                exemplaire du Contrat,</p></li>
                <li>
        <p>d'un
                    avertissement relatif à la restriction de garantie et de
                responsabilité du  Concédant telle que
                        prévue aux articles <a href="#responsabilite">8</a> et 
                        <a href="#garantie">9</a>,</p>
                </li>
            </ol>
            <p>et que, dans le cas où seul le code objet du Logiciel
                Modifié est redistribué, le Licencié permette un accès effectif
            à son code source complet pendant au moins toute
                la durée de sa distribution du Logiciel Modifié, étant entendu que 
                le coût	additionnel d'acquisition du code source ne devra pas excéder
                le simple coût de transfert des données.</p> 
                

                </div>

                
        
                
                <div class="subclause">
                <h5>
        <a name="distrib-derive"></a>5.3.3 DISTRIBUTION DU LOGICIEL DERIVE</h5>

                <p>Lorsque le Licencié crée un
                Logiciel Dérivé, ce Logiciel Dérivé peut être distribué sous un
                contrat de licence autre que le présent Contrat à
                condition de respecter les obligations de mention des droits sur le
                Logiciel telles
                que définies à l'article <a href="#mention">6.4</a>. 
                Dans le cas où la création du Logiciel Dérivé a 
                nécessité une modification du Code Source le licencié s'engage à ce
                que:
                </p>
                <ol class="decorated-list">
                <li> le Logiciel Modifié 
                correspondant à cette modification soit régi par le
                présent Contrat,</li>
                <li>les Contributions Intégrées dont le Logiciel Modifié résulte
                soient clairement identifiées et documentées,</li>
                <li>le Licencié permette un accès effectif au code source du Logiciel
                Modifié, pendant au moins toute la durée de la distribution du 
                Logiciel Dérivé, de telle
                sorte que ces modifications puissent être reprises dans une version
                ultérieure du Logiciel, étant entendu que le coût additionnel
                d'acquisition du code source du Logiciel Modifié ne devra pas 
                excéder le simple coût du transfert des données.</li>
                </ol>
                </div>
                

                

                <div class="subclause">

                

                

                
                <h5>
        <a name="compatibilite"></a>5.3.4 COMPATIBILITE AVEC LA LICENCE CeCILL</h5>
                <p>
                Lorsqu'un Logiciel Modifié contient une Contribution Intégrée soumise
                au contrat de licence CeCILL, ou lorsqu'un Logiciel Dérivé contient
                un Module Lié soumis au contrat de licence CeCILL,
                les stipulations prévues au troisième item de l'article 
                <a href="#mention">6.4</a> sont facultatives. 
                </p>
                


                </div>

                </div>

        </div>
        <div class="article">

                <h3> Article 6  - PROPRIETE INTELLECTUELLE</h3>

                <div class="clause">
                <h4>
        6.1 SUR LE LOGICIEL INITIAL</h4>

            <p>Le
            Titulaire est détenteur des droits patrimoniaux sur le
            Logiciel Initial. Toute utilisation du Logiciel Initial est soumise
            au respect des conditions dans lesquelles le Titulaire a choisi de
            diffuser son oeuvre et nul autre n'a la faculté de
            modifier les conditions de diffusion de ce Logiciel Initial. 
            </p>
            <p>Le
            Titulaire s'engage à 
                ce que le Logiciel Initial 
                reste  au moins régi par le Contrat
                et ce, pour la durée visée à l'article <a href="#duree">4.2</a>.</p>

                </div>

                

                
                <div class="clause">
                <h4>
        6.2 SUR LES CONTRIBUTIONS INTEGREES</h4>

                <p>Le Licencié qui a développé une Contribution Intégrée 
                est
                titulaire sur celle-ci des droits de propriété intellectuelle dans
                les conditions définies par la législation applicable.</p>
                </div>

                <div class="clause">
                <h4>
        6.3 SUR LES MODULES LIES</h4>

                <p>Le Licencié qui a développé un Module Lié
                est titulaire sur celui-ci des droits de propriété intellectuelle dans
                les conditions définies par la législation applicable et reste libre
                du choix du contrat régissant sa diffusion dans les conditions
                définies à l'article <a href="#distrib-derive">5.3.3</a>.</p>
                </div>
            
                

                <div class="clause">
                
                
            <h4>
        <a name="mention"></a>6.4 MENTIONS DES DROITS</h4>
                

                <div class="subclause">
            <p>
                Le Licencié s'engage expressément:</p>
            <ol class="decorated-list">
                <li>
        <p>à
            ne pas supprimer ou modifier de quelque manière que ce soit
            les mentions de propriété intellectuelle apposées
            sur le Logiciel;</p>
                </li>
            <li>
        <p>à reproduire à l'identique lesdites mentions de
            propriété intellectuelle sur les copies du Logiciel modifié ou
                
                non;</p> 
                </li>
                
                <li> à faire en sorte que l'utilisation du Logiciel, ses mentions de
                propriété intellectuelle et le fait qu'il est régi par le Contrat
                soient indiqués dans un texte facilement accessible notamment depuis
                l'interface de tout Logiciel Dérivé.  
                </li>
                
            </ol>
                </div>

                <div class="subclause">
            <p>Le
            Licencié s'engage à ne pas porter atteinte,
            directement ou indirectement, aux droits de propriété
            intellectuelle du Titulaire et/ou des Contributeurs 
                sur le Logiciel et à
            prendre, le cas échéant, à l'égard
            de son personnel toutes les mesures nécessaires pour assurer
            le respect des dits droits de propriété intellectuelle
            du Titulaire et/ou des Contributeurs.</p>
                </div>

                </div>

        </div>
        <div class="article">

                <h3> Article 7  - SERVICES ASSOCIES</h3>

                <div class="clause">
            <p>7.1 Le
            Contrat n'oblige en aucun cas le Concédant à la
            réalisation de prestations d'assistance technique ou de
            maintenance du Logiciel.</p>
            <p>Cependant
            le Concédant reste libre de proposer ce type de services. Les
            termes et conditions d'une telle assistance technique et/ou
            d'une telle maintenance seront alors déterminés
            dans un acte séparé. Ces actes de maintenance et/ou
            assistance technique n'engageront que la seule responsabilité
            du Concédant qui les propose.</p>
                </div>
        
                <div class="clause">
            <p>7.2 De
            même, tout Concédant est libre de proposer, sous sa
            seule responsabilité, à ses licenciés une
            garantie, qui n'engagera que lui, lors de la redistribution du
            Logiciel et/ou du Logiciel Modifié et ce, dans les conditions
            qu'il souhaite. Cette garantie et les modalités
            financières de son application feront l'objet d'un
            acte séparé entre le Concédant et le Licencié.</p>
                </div>

        </div>
        <div class="article">

                <h3>
        <a name="responsabilite"></a> Article 8  - RESPONSABILITE</h3>

                <div class="clause">
            <p>8.1 Sous
            réserve des dispositions de
                l'article <a href="#limite-responsabilite">8.2</a>, 
                le Licencié a la faculté, sous réserve de prouver la faute du
            Concédant concerné, de solliciter la réparation
            du préjudice direct qu'il subirait du fait du
                Logiciel et dont il apportera la preuve.
                </p>
                </div>

                <div class="clause">
            <p><a name="limite-responsabilite"></a>8.2 
                La
            responsabilité du Concédant est limitée aux
            engagements pris en application du Contrat et ne saurait être
            engagée en raison notamment: (i) des dommages dus à
            l'inexécution, totale ou partielle, de ses obligations
            par le Licencié, (ii) des dommages directs ou indirects
            découlant de l'utilisation ou des performances du
            Logiciel subis par le Licencié 
                et  (iii) 
                plus généralement d'un quelconque
                dommage
                indirect. 
                En particulier, les Parties
            conviennent expressément que tout préjudice financier
            ou commercial (par exemple perte de données, perte de
            bénéfices, perte d'exploitation, perte de
            clientèle ou de commandes, manque à gagner, trouble
            commercial quelconque) ou  toute action dirigée contre le
            Licencié par un tiers, constitue un dommage indirect et
            n'ouvre pas droit à réparation par le
                Concédant.
                </p> 
                </div>

        </div>
        <div class="article">

                <h3>
        <a name="garantie"></a> Article 9  - GARANTIE</h3>

                <div class="clause">
            <p>9.1 
            Le
            Licencié reconnaît que l'état actuel des
            connaissances scientifiques et techniques au moment de la mise en
            circulation du Logiciel ne permet pas d'en tester et d'en
            vérifier toutes les utilisations ni de détecter
            l'existence d'éventuels défauts.
            L'attention du Licencié a été attirée
            sur ce point sur les risques associés au chargement, à
            l'utilisation, la modification et/ou au développement
            et à la reproduction du Logiciel qui sont réservés
            à des utilisateurs avertis.</p>
            <p>Il
            relève de la responsabilité du Licencié de
            contrôler, par tous moyens, l'adéquation du
            produit à ses besoins, son bon fonctionnement et de s'assurer
            qu'il ne causera pas de dommages aux personnes et aux biens.
            </p>
                </div>

                <div class="clause">
            <p><a name="bonne-foi"></a>9.2 
            Le Concédant déclare de bonne foi être en droit
            de concéder l'ensemble des droits attachés au Logiciel
            (comprenant notamment les droits visés à l'article 
                <a href="#etendue">5</a>).
                </p> 
                </div>

                <div class="clause">
            <p>9.3 Le
            Licencié reconnaît que le Logiciel est fourni "en
            l'état" par le Concédant sans autre
            garantie, expresse ou tacite, que celle prévue à
            l'article <a href="#bonne-foi">9.2</a> et notamment sans aucune garantie sur sa
                valeur commerciale, son caractère sécurisé, innovant
            ou pertinent.
            </p>
            <p>En
            particulier, le Concédant ne garantit pas que le Logiciel est
            exempt d'erreur, qu'il fonctionnera sans interruption,
                qu'il 
            sera compatible avec l'équipement du Licencié et
            sa configuration logicielle ni qu'il remplira les besoins du
            Licencié.</p>
                </div>

                <div class="clause">
            <p>9.4 Le
            Concédant ne garantit pas, de manière expresse ou
            tacite, que le Logiciel ne porte pas atteinte à un quelconque
            droit de propriété intellectuelle d'un tiers
            portant sur un brevet, un logiciel ou sur tout autre droit de
            propriété. Ainsi, le Concédant exclut toute
            garantie au profit du Licencié contre les actions en
            contrefaçon qui pourraient être diligentées au
            titre de l'utilisation, de la modification, et de la
            redistribution du Logiciel. Néanmoins, si de telles actions
            sont exercées contre le Licencié, le Concédant
            lui apportera son aide technique et juridique pour sa défense.
            Cette aide technique et juridique est déterminée au
            cas par cas entre le Concédant concerné et le
                Licencié 
            dans le cadre d'un protocole d'accord. Le Concédant
            dégage toute responsabilité quant à
            l'utilisation de la dénomination du Logiciel par le
            Licencié. Aucune garantie n'est apportée quant
                à 
            l'existence de droits antérieurs sur le nom du Logiciel
            et sur l'existence d'une marque.</p>
                </div>

        </div>
        <div class="article">

                <h3> Article 10  - RESILIATION</h3>
            
                <div class="clause">
            <p>10.1 En
            cas de manquement par le Licencié aux obligations mises à
            sa charge par le Contrat, le Concédant pourra résilier
            de plein droit le Contrat trente (30) jours après
            notification adressée au Licencié et restée
            sans effet.</p>
                </div>

                <div class="clause">
            <p>10.2 Le
            Licencié dont le Contrat est résilié n'est
            plus autorisé à utiliser, modifier ou distribuer le
            Logiciel. Cependant, toutes les licences qu'il aura
                concédées 
            antérieurement à la résiliation du Contrat
            resteront valides sous réserve qu'elles aient
                été 
            effectuées en conformité avec le Contrat.</p>
                </div>

        </div>
        <div class="article">

                <h3> Article 11  - DISPOSITIONS DIVERSES</h3>

                <div class="clause">
                <h4>
        11.1 CAUSE EXTERIEURE</h4>

            <p>Aucune
            des Parties ne sera responsable d'un retard ou d'une
            défaillance d'exécution du Contrat qui serait dû
            à un cas de force majeure, un cas fortuit ou une cause
            extérieure, telle que, notamment, le mauvais fonctionnement
            ou les interruptions du réseau électrique ou de
            télécommunication, la paralysie du réseau liée
            à une attaque informatique, l'intervention des
            autorités gouvernementales, les catastrophes naturelles, les
            dégâts des eaux, les tremblements de terre, le feu, les
            explosions, les grèves et les conflits sociaux, l'état
            de guerre...</p>
                </div>
        
                <div class="clause">
            <p>11.2 Le
            fait, par l'une ou l'autre des Parties, d'omettre
            en une ou plusieurs occasions de se prévaloir d'une ou
            plusieurs dispositions du Contrat, ne pourra en aucun cas impliquer
            renonciation par la Partie intéressée à s'en
            prévaloir ultérieurement.</p>
                </div>

                <div class="clause">
            <p>11.3 Le
            Contrat annule et remplace toute convention antérieure,
            écrite ou orale, entre les Parties sur le même objet et
            constitue l'accord entier entre les Parties sur cet objet.
            Aucune addition ou modification aux termes du Contrat n'aura
            d'effet à l'égard des Parties à
            moins d'être faite par écrit et signée par
            leurs représentants dûment habilités.</p>
                </div>

                <div class="clause">
            <p>11.4 Dans
            l'hypothèse où une ou plusieurs des dispositions
            du Contrat s'avèrerait contraire à une loi ou à
            un texte applicable, existants ou futurs, cette loi ou ce texte
            prévaudrait, et les Parties feraient les amendements
            nécessaires pour se conformer à cette loi ou à
            ce texte. Toutes les autres dispositions resteront en vigueur. De
            même, la nullité, pour quelque raison que ce soit,
            d'une des dispositions du Contrat ne saurait entraîner
            la nullité de l'ensemble du Contrat.</p>
                </div>

                <div class="clause">
                <h4>
        11.5 LANGUE</h4>
            <p>Le
            Contrat est rédigé en langue française et en
            langue anglaise, ces deux versions 
                faisant également foi.
                </p>
                </div>

        </div>
        <div class="article">
                <h3> Article 12  - NOUVELLES VERSIONS DU CONTRAT</h3>
                <div class="clause">
            <p>12.1 Toute personne est autorisée à copier et distribuer des
            copies de ce Contrat.</p>
                </div>
                <div class="clause">
            <p>12.2 Afin	d'en préserver la cohérence, le texte du Contrat
            est protégé et ne peut être modifié que
            par les auteurs de la licence, lesquels se réservent le droit
            de publier périodiquement des mises à jour ou de
            nouvelles versions du Contrat, qui posséderont chacune un
            numéro distinct. Ces versions ultérieures seront
            susceptibles de prendre en compte de nouvelles problématiques
            rencontrées par les logiciels libres.</p>
                </div>
                <div class="clause">
            <p>12.3 Tout
            Logiciel diffusé sous une version donnée du Contrat ne
            pourra faire l'objet d'une diffusion ultérieure que sous la
            même version du Contrat ou une version postérieure.</p>
                </div>
        </div>
        <div class="article">	
                <h3> Article 13  - LOI APPLICABLE ET COMPETENCE TERRITORIALE</h3>
                <div class="clause">
            <p>13.1 
            Le Contrat est régi par la loi
            française. Les Parties conviennent de tenter de régler
            à l'amiable les différends ou litiges qui
            viendraient à se produire par suite ou à l'occasion
            du Contrat.
            </p>
                </div>
                <div class="clause">
                <p>13.2 
            A défaut d'accord amiable dans un délai de deux
            (2) mois à compter de leur survenance et sauf situation
            relevant d'une procédure d'urgence, les
            différends ou litiges seront portés par la Partie la
            plus diligente devant les Tribunaux compétents de
                Paris.</p>
                </div>
        </div>
        <div class="footnote">
            <p><a name="footnote1">
                1 CeCILL est pour
                Ce(a) C(nrs) I(nria) L(ogiciel) L(ibre)</a></p>
        </div>
        <div class="version">Version 1.0 du 2006-09-05.</div>
    </main>
    <?php (new FooterCreator)->create() ?>
</body>
</html>


    