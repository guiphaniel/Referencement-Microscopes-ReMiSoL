<?php
    include_once(__DIR__ . "/../../include/config.php");
    include_once(__DIR__ . "/Creator.php");

    Abstract Class FormCreator implements Creator {
        public abstract function createBody();

        function __construct(private string $action, private string $method, private string $enctype = "") {}

        function begin() {
            if(isset($_SESSION["form"]["errorMsg"])) : ?>
                <p id="error-msg"><?= $_SESSION["form"]["errorMsg"] ?></p>
            <?php endif; unset($_SESSION["form"]["errorMsg"]); ?>
            <form action=<?=$this->action?> method=<?=$this->method?> <?php if(!empty($this->enctype)) echo "method=$this->enctype"; ?>>
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