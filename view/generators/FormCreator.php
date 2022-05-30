<?php
    include_once(__DIR__ . "/../../include/config.php");
    include_once(__DIR__ . "/Creator.php");

    Abstract Class FormCreator implements Creator {
        public abstract function createBody();

        static function handleMsg() {
            if(isset($_SESSION["form"]["infoMsg"])) : ?>
                <p class="info-msg"><?= $_SESSION["form"]["infoMsg"] ?></p>
            <?php endif; unset($_SESSION["form"]["infoMsg"]);

            if(isset($_SESSION["form"]["errorMsg"])) : ?>
                <p class="error-msg"><?= $_SESSION["form"]["errorMsg"] ?></p>
            <?php endif; unset($_SESSION["form"]["errorMsg"]);
        }

        function __construct(private string $action, private string $method, private string $enctype = "") {}

        function begin() {
            $this->handleMsg() ?>
            <form action=<?=$this->action?> method=<?=$this->method?> <?php if(!empty($this->enctype)) echo "enctype=$this->enctype"; ?>>
            <?php
        }

        function end() {
            echo "</form>";
        }

        function create() {
            $this->begin();
            $this->createBody();
            $this->end();
        }
    }