<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/FormCreator.php");
    include_once(__DIR__ . "/../../model/services/UserService.php");

    Class UserFormCreator extends FormCreator {
        private $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland
        private $userId;

        function __construct(private User $user, private bool $adminView) {
            parent::__construct("processing/user_processing.php", "post", "");

            $this->userId = $this->user->getId();
        }

        public function createBody() {
            ?>
                <input type="hidden" name="id" value="<?=$this->userId?>">
                <input type="hidden" name="action" value="update">
                <div class="input-wrapper">
                    <input id="firstname-<?=$this->userId?>" type="text" autocomplete="given-name" name="firstname" value="<?=htmlspecialchars($this->user->getFirstname())?>" placeholder=" " required>
                    <label for="firstname-<?=$this->userId?>">Prénom</label>
                </div>
                <div class="input-wrapper">
                    <input id="lastname-<?=$this->userId?>" type="text" autocomplete="family-name" autocapitalize="characters" name="lastname" value="<?=htmlspecialchars($this->user->getLastname())?>" placeholder=" " required>
                    <label for="lastname-<?=$this->userId?>">NOM</label>
                </div>
                <div class="input-wrapper">
                    <input id="email-<?=$this->userId?>" type="email" autocomplete="email" name="email" value="<?=htmlspecialchars($this->user->getEmail())?>" placeholder=" " required>
                    <label for="email-<?=$this->userId?>">Courriel</label>
                </div>
                <div class="select-input">
                    <select id="phone-code-<?=$this->userId?>" name="phoneCode" autocomplete="tel-country-code">
                        <?php 
                    foreach ($this->phoneCodes as $phoneCode) : ?>
                        <option value="<?=$phoneCode;?>"<?= str_contains($phoneCode, $this->user->getPhoneCode()) ? " selected" : "";?>><?=$phoneCode?></option>
                        <?php endforeach; ?>
                    </select>
                    <div class="input-wrapper">
                        <input id="phone-<?=$this->userId?>" type="text" name="phoneNum" autocomplete="tel-national" value="<?=$this->user->getPhoneNum()?>" pattern="0?\d{9}" placeholder=" " required>
                        <label for="phone-<?=$this->userId?>">Télephone</label>
                    </div>
                </div>
                <div class="input-wrapper">
                    <input id="password1-<?=$this->userId?>" type="password" autocomplete="new-password" name="password1" placeholder=" ">
                    <label for="password1-<?=$this->userId?>">Mot de passe</label>
                </div>
                <div class="input-wrapper">
                    <input id="password2-<?=$this->userId?>" type="password" name="password2" placeholder=" ">
                    <label for="password2-<?=$this->userId?>">Vérification du mot de passe</label>
                </div>
                <?php if($this->adminView): ?>
                    <div class="checkbox-group">
                        <input type="checkbox" id="locked-<?=$this->userId?>" name="locked" <?= $this->user->isLocked() ? "checked" : "";?>>
                        <svg class="checkmark">
                            <polyline points="1,5 6,9 14,1"></polyline>
                        </svg>
                        <label for="locked-<?=$this->userId?>">Verrouillé</label>
                    </div>
                    <div class="checkbox-group">
                        <input type="checkbox" id="admin-<?=$this->userId?>" name="admin" <?= $this->user->isAdmin() ? "checked" : "";?>>
                        <svg class="checkmark">
                            <polyline points="1,5 6,9 14,1"></polyline>
                        </svg>
                        <label for="admin-<?=$this->userId?>">Administrateur</label>
                    </div>
                <?php endif; ?>
                <input type="submit" class="bt">
            <?php
        }

        function end() { ?>
            </form>
            <form action="/processing/user_processing.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?=$this->userId?>">
                <div class="bt rm-bt">Supprimer le compte</div>
            </form>
            </div>
            <script src="public/js/password_validation.js"></script>
        <?php 
        }
    }