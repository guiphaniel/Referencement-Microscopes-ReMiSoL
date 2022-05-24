<?php
    include_once(__DIR__ . "/../../include/config.php");
    include_once(__DIR__ . "/Creator.php");

    Class HeaderCreator implements Creator {
        function __construct(private string $title) {}

        public function create() {
            ?>
            <header>
                <nav>
                    <ul>
                        <li><a href="/index.php">Accueil</a></li>
                        <?php if(isUserSessionValid()): ?>
                            <li><a href="/form.php">Formulaire</a></li>
                            <li><a href="/account.php">Mon compte</a></li>
                            <?php if($_SESSION["user"]["admin"]): ?>
                                <li><a href="/admin.php">Administration</a></li>
                            <?php endif; ?>
                            <li><a href="/processing/logout.php">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="/signin.php">Inscription</a></li>
                            <li><a href="/login.php">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <h1><?=$this->title?></h1>
            </header>
            <?php
        }
    }