<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/../../model/services/UserService.php");
    include_once(__DIR__ . "/Creator.php");

    Class FooterCreator implements Creator {
        public function create() {
            $email = UserService::getInstance()->findAllAdmins()[0]->getEmail();?>
            <footer>
                <nav>
                    <ul>
                        <li class="<?= $this->isActive('/legal-infos.php') ?>"><a href="/legal-infos.php">Mentions légales</a></li>
                        <li class="<?= $this->isActive('/contact.php') ?>"><a href="/contact.php">Contact</a></li>
                        <li>
                        <address>
                            <a href="mailto:<?=$email?>"><?=$email?></a>
                        </address>
                        </li>
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