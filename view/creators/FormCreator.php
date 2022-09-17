<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/Creator.php");

    Abstract Class FormCreator implements Creator {
        public abstract function createBody();

        static function handleMsg() {
            if(isset($_SESSION["form"]["infoMsg"])) : ?>
            <div class="msg-wrapper">
                <p class="msg info-msg"><?= nl2br($_SESSION["form"]["infoMsg"]) ?></p>
            </div>
            <?php endif; unset($_SESSION["form"]["infoMsg"]);

            if(isset($_SESSION["form"]["errorMsg"])) : ?>
            <div class="msg-wrapper">
                <p class="msg error-msg"><?= nl2br($_SESSION["form"]["errorMsg"]) ?></p>
            </div>
            <?php endif; unset($_SESSION["form"]["errorMsg"]);
        }

        function __construct(private string $action, private string $method, private string $enctype = "", private bool $bigForm = false) {}

        function begin() {
            $this->handleMsg() ?>
            <div class="form-wrapper">
                <form <?= $this->bigForm ? 'class="big-form"' : "" ?> action=<?=$this->action?> method=<?=$this->method?> <?php if(!empty($this->enctype)) echo "enctype=$this->enctype"; ?>>
            <?php
        }

        function end() {
            echo "</form>";
            echo "</div>";
        }

        function create() {
            $this->begin();
            $this->createBody();
            $this->end();
        }

        function createInput($id, $name, $label, $value = null, $type="text", $required = true, $class=null) {  
            $value = htmlspecialchars($value)?>
            <div class="input-wrapper">
                <input id="<?=$id?>" <?= isset($class) ? "class='$class'" : ""?> type="<?=$type?>" name="<?=$name?>" placeholder=" " <?= isset($value) ? "value='$value'" : ""?> <?=$required ? "required" : ""?>>
                <label for="<?=$id?>"><?=$label?></label>
            </div>
        <?php
        }

        function createInputRm($id, $name, $label, $value = null, $type="text", $required = true, $class=null) {  ?>
            <div class="input-rm-wrapper">
                <?php $this->createInput($id, $name, $label, $value, $type, $required, $class) ?>
                <div class="bt rm-bt"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg></div>
            </div>
        <?php
        }
    }