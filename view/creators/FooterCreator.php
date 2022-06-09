<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/../../model/services/UserService.php");
    include_once(__DIR__ . "/Creator.php");

    Class FooterCreator implements Creator {
        public function create() { ?>
            <footer>
                <nav>
                    <ul>
                        <li><a href="/legal-infos.php" class="<?= $this->isActive('/legal-infos.php') ?>">Mentions légales</a></li>
                        <li><a href="/contact.php" class="<?= $this->isActive('/contact.php') ?>">Nous contacter</a></li>
                        <li><a href="/api/v1/reference.php" class="<?= $this->isActive('/api/v1/reference.php') ?>">API</a></li>
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