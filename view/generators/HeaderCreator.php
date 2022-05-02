<?php
    include_once("Creator.php");

    Class HeaderCreator implements Creator {
        function __construct(private string $title) {}

        public function create() {
            ?>
            <header>
                <nav>
                    <ul>
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="form.php">Formulaire</a></li>
                        <li><a href="signin.php">Inscription</a></li>
                        <li><a href="login.php">Connexion</a></li>
                    </ul>
                </nav>
                <h1><?=$this->title?></h1>
            </header>
            <?php
        }
    }