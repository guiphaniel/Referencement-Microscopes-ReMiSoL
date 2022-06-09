<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/../../model/services/UserService.php");
    include_once(__DIR__ . "/Creator.php");

    Class FooterCreator implements Creator {
        public function create() { ?>
            <footer>
                <nav>
                    <ul>
                        <li class="<?= $this->isActive('/legal-infos.php') ?>"><a href="/legal-infos.php">Mentions légales</a></li>
                        <li class="<?= $this->isActive('/contact.php') ?>"><a href="/contact.php">Nous contacter</a></li>
                    </ul>
                </nav>
            </footer>
            <?php
        }

        private function isActive($link) {
            if ($_SERVER['PHP_SELF'] == $link)
                return 'active';
        }
    }