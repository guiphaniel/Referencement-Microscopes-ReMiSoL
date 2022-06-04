<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/Creator.php");

    Class HeaderCreator implements Creator {
        function __construct(private string $title, private string $searchContent = "") {}

        public function create() {
            ?>
            <header>
                <div id="navigation">
                    <div id="navbar">
                    <input id="hamburger-check" type="checkbox">
                    <div class="hamburger"></div>
                    <div class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M575.8 255.5C575.8 273.5 560.8 287.6 543.8 287.6H511.8L512.5 447.7C512.5 450.5 512.3 453.1 512 455.8V472C512 494.1 494.1 512 472 512H456C454.9 512 453.8 511.1 452.7 511.9C451.3 511.1 449.9 512 448.5 512H392C369.9 512 352 494.1 352 472V384C352 366.3 337.7 352 320 352H256C238.3 352 224 366.3 224 384V472C224 494.1 206.1 512 184 512H128.1C126.6 512 125.1 511.9 123.6 511.8C122.4 511.9 121.2 512 120 512H104C81.91 512 64 494.1 64 472V360C64 359.1 64.03 358.1 64.09 357.2V287.6H32.05C14.02 287.6 0 273.5 0 255.5C0 246.5 3.004 238.5 10.01 231.5L266.4 8.016C273.4 1.002 281.4 0 288.4 0C295.4 0 303.4 2.004 309.5 7.014L564.8 231.5C572.8 238.5 576.9 246.5 575.8 255.5L575.8 255.5z"/></svg>
                    </div>
                    <div id="account-bt" class="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M224 256c70.7 0 128-57.31 128-128s-57.3-128-128-128C153.3 0 96 57.31 96 128S153.3 256 224 256zM274.7 304H173.3C77.61 304 0 381.6 0 477.3c0 19.14 15.52 34.67 34.66 34.67h378.7C432.5 512 448 496.5 448 477.3C448 381.6 370.4 304 274.7 304z"/></svg>
                    </div>
                    <nav>
                        <ul>
                            <li><a href="/index.php">Accueil</a></li>
                            <li><a href="/presentation.php">Présentation</a></li>
                            <?php if(isUserSessionValid()): ?>
                                <li><a href="/form.php">Formulaire</a></li>
                                <?php if($_SESSION["user"]["admin"]): ?>
                                    <li><a href="/admin.php">Administration</a></li>
                                <?php endif; ?>
                                <li><a href="/account.php">Mon compte</a></li>
                                <li><a href="/processing/logout.php">Déconnexion</a></li>
                            <?php else: ?>
                                <li><a href="/signin.php">Inscription</a></li>
                                <li><a href="/login.php">Connexion</a></li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <form id="search-wrapper" action="/search.php">
                    <label id="search-label" for="search-input"></label>
                    <div id="searchbar">
                        <input id="search-input" type="search" name="filters" value="<?=$this->searchContent?>" placeholder="Effectuez une recherche">
                        <span id="search-bt">
                            <input enterkeyhint="go" type="submit" value="Rechercher">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M500.3 443.7l-119.7-119.7c27.22-40.41 40.65-90.9 33.46-144.7C401.8 87.79 326.8 13.32 235.2 1.723C99.01-15.51-15.51 99.01 1.724 235.2c11.6 91.64 86.08 166.7 177.6 178.9c53.8 7.189 104.3-6.236 144.7-33.46l119.7 119.7c15.62 15.62 40.95 15.62 56.57 0C515.9 484.7 515.9 459.3 500.3 443.7zM79.1 208c0-70.58 57.42-128 128-128s128 57.42 128 128c0 70.58-57.42 128-128 128S79.1 278.6 79.1 208z"/></svg>
                        </span>
                    </div>
                </form>
                </div>
                
                <h1><?=$this->title?></h1>
            </header>
            <?php
        }
    }