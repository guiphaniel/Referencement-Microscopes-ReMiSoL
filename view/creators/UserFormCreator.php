<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/FormCreator.php");
    include_once(__DIR__ . "/../../model/services/UserService.php");

    Class UserFormCreator extends FormCreator {
        private $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

        function __construct(private User $user, private bool $adminView) {
            parent::__construct("processing/user_processing.php", "post", "");
        }

        public function createBody() {
            ?>
                <input type="hidden" name="id" value="<?=UserService::getInstance()->getUserId($this->user)?>">
                <input type="hidden" name="action" value="update">
                <div class="input-wrapper">
                    <input id="firstname" type="text" autocomplete="given-name" name="firstname" value="<?=$this->user->getFirstname()?>" placeholder=" " required>
                    <label for="firstname">Prénom</label>
                </div>
                <div class="input-wrapper">
                    <input id="lastname" type="text" autocomplete="family-name" autocapitalize="characters" name="lastname" value="<?=$this->user->getLastname()?>" placeholder=" " required>
                    <label for="lastname">NOM</label>
                </div>
                <div class="input-wrapper">
                    <input id="email" type="email" autocomplete="email" name="email" value="<?=$this->user->getEmail()?>" placeholder=" " required>
                    <label for="email">Courriel</label>
                </div>
                <div class="select-input">
                    <select id="phone-code" name="phoneCode" autocomplete="tel-country-code">
                        <?php 
                    foreach ($this->phoneCodes as $phoneCode) : ?>
                        <option value="<?=$phoneCode;?>"<?= str_contains($phoneCode, $this->user->getPhoneCode()) ? " selected" : "";?>><?=$phoneCode?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-wrapper">
                        <input id="phone" type="text" name="phoneNum" autocomplete="tel-national" value="<?=$this->user->getPhoneNum()?>" placeholder=" " required>
                        <label for="phone">Télephone</label>
                    </div>
                </div>
                <div class="input-wrapper">
                    <input id="password1" type="password" autocomplete="new-password" name="password1" placeholder=" ">
                    <label for="password1">Mot de passe</label>
                </div>
                <div class="input-wrapper">
                    <input id="password2" type="password" name="password2" placeholder=" ">
                    <label for="password2">Vérification du mot de passe</label>
                </div>
                <?php if($this->adminView): ?>
                    <div class="checkbox-group">
                        <input type="checkbox" id="locked" name="locked" <?= $this->user->isLocked() ? "checked" : "";?>>
                        <svg class="checkmark">
                            <polyline points="1,5 6,9 14,1"></polyline>
                        </svg>
                        <label for="locked">Verrouillé</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="admin" name="admin" <?= $this->user->isAdmin() ? "checked" : "";?>>
                        <svg class="checkmark">
                            <polyline points="1,5 6,9 14,1"></polyline>
                        </svg>
                        <label for="admin">Administrateur</label>
                    </div>
                <?php endif; ?>
                <input type="submit" class="bt">
                <div class="rm-bt"></div>
            <?php
        }
    }