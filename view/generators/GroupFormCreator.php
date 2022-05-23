<?php
    include_once(__DIR__ . "/../../include/config.php");
    include_once(__DIR__ . "/../../utils/browser_supports_webp.php");
    include_once(__DIR__ . "/FormCreator.php");
    include_once(__DIR__ . "/../../model/services/MicroscopesGroupService.php");
    include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

    Class GroupFormCreator extends FormCreator {
        private $labTypes = ["UPR", "UMR", "IRL", "UAR", "FR", "EMR"];
        private $countries = ["Belgique", "France", "Suisse"]; // Belgium, France, Switzerland
        private $phoneCodes = ["+32 (Belgique)", "+33 (France)", "+41 (Suisse)"]; // Belgium, France, Switzerland

        function __construct(private $group = null) {
            parent::__construct("processing/group_form_processing.php", "post", "multipart/form-data");
        }

        public function createBody() {
            $this->createMicroGroupIdHiddenInput($this->group?->getId());
            $this->createLabField($this->group?->getLab());
            ?>
            <fieldset id="contacts">
                <legend>Référent·e·s</legend>
                <?php
                //create the first, not removable contact field
                $this->createContactField(0, $this->group?->getContacts()[0], false);
                //create all others, removable contact fields
                foreach ($this->group?->getContacts()??[] as $key => $contact) {
                    if($key == 0)
                        continue;

                    $this->createContactField($key, $contact, true);
                } 
                ?>
                <div id="add-contact" class="add-bt"></div>
            </fieldset>
            <?php
            $this->createCoorField($this->group?->getCoor());
            ?>

            <fieldset id="micros">
                <legend>Microscopes</legend>
                <!-- Compagnies datalist -->
                <datalist id="micro-compagnies">
                <?php 
                    foreach (CompagnyService::getInstance()->getAllCompagnies() as $compagny): ?>
                        <option value="<?=$compagny->getName()?>">
                    <?php endforeach; ?>
                </datalist>
                <!-- Keywords datalists -->
                <?php 
                    $keyWordService = KeywordService::getInstance();
                    $cats = $keyWordService->getAllCategories();
                    foreach ($cats as $cat): 
                        $catName = $cat->getName();
                        echo "<!-- $catName datalist -->" ?>
                        <datalist id="cats-<?=HTMLNormalize($catName)?>">
                        <?php 
                            $tags = $keyWordService->getAllTags($cat);
                            foreach ($tags as $tag): ?>
                                <option value="<?=$tag?>">
                            <?php endforeach; ?>
                        </datalist>
                    <?php endforeach; 
                    
                    //create all micro fields (only the first one isn't removable)
                    $first = true;  
                    foreach ($this->group?->getMicroscopes()??[1 => null] as $microId => $micro) {
                        $this->createMicroField($microId, $micro, !$first);

                        if($first)
                            $first = false;
                    }  
                ?>
                <div id="add-micro" class="add-bt"></div>
            </fieldset>
            <input type="submit">
            <?php
        }

    private function valueOf($value) {
        if(isset($value))
            return "value='$value'";
        else 
            return null;
    }

    private function selectCountry($country) {
        if(isset($this->group)) {
            if($country == $this->group->getLab()->getAddress()->getCountry())
                return "selected";
        } else {
            if($country == "France")
                return "selected";
        }

        return "";
    }

    private function selectPhoneCode($contact, $phoneCode) {
        if($contact?->getPhoneCode() == $phoneCode)
            return "selected";
        else {
            if($phoneCode == "+33")
                return "selected";
        }

        return "";
    }

    private function createMicroGroupIdHiddenInput($groupId) {
        if(!isset($groupId)) 
            return;

        ?>
            <input type="hidden" name="id" value="<?=$groupId?>">
        <?php
    }

    private function createLabField($lab) {
        ?>
            <fieldset>
                <legend>Laboratoire / service</legend>
                <address>
                    <label for="lab-name">Nom du laboratoire</label>
                    <input id="lab-name" type="text" name="lab[name]" autocomplete="organization" <?=$this->valueOf($lab?->getName())?> required>
                    <label for="lab-code">Code</label>
                    <select name="lab[type]" id="lab-type" required>
                    <?php foreach ($this->labTypes as $labType) : ?>
                        <option value=<?=$labType;?> <?= $lab?->getType() == $labType ? "selected" : "" ?>><?=$labType?></option>
                    <?php endforeach; ?>
                    </select>
                    <input id="lab-code" type="number" name="lab[code]" min="10" max="9999" <?=$this->valueOf($lab?->getCode())?> required>
                    <label for="lab-address-school">Université / École</label>
                    <input id="lab-address-school" type="text" name="lab[address][school]" <?=$this->valueOf($lab?->getAddress()->getSchool())?>>
                    <label for="lab-address-street">Adresse</label>
                    <input id="lab-address-street" type="text" name="lab[address][street]" autocomplete="address-line1" <?=$this->valueOf($lab?->getAddress()->getStreet())?> required>
                    <label for="lab-address-zip">Code postal</label>
                    <input id="lab-address-zip" type="text" name="lab[address][zipCode]" autocomplete="postal-code" <?=$this->valueOf($lab?->getAddress()->getZipCode())?> required>
                    <label for="lab-address-city">Ville</label>
                    <input id="lab-address-city" type="text" name="lab[address][city]" autocomplete="address-level2" <?=$this->valueOf($lab?->getAddress()->getCity())?> required>
                    <label for="lab-address-country">Pays</label>
                    <select name="lab[address][country]" id="lab-address-country" autocomplete="country">
                    <?php foreach ($this->countries as $country) : ?>
                        <option value="<?=$country;?>"<?=$this->selectCountry($country)?>><?=$country?></option>
                    <?php endforeach; ?>
                    </select>
                    <label for="lab-website">Site web</label>
                    <input id="lab-website" type="url" name="lab[website]" autocomplete="url" <?=$this->valueOf($lab?->getWebsite())?> required>
                </address>
            </fieldset>   
        <?php
    }

    private function createContactField($fieldId, $contact, bool $removable) {
        $id = $contact?->getId()??0;
        ?>
            <fieldset id="contact-field-<?=$id?>" class="contact-field">
                <legend>Référent·e n°<?=$fieldId + 1?></legend>
                <address>
                    <label for="contact-firstname-<?=$id?>">Prénom</label>
                    <input id="contact-firstname-<?=$id?>" type="text" name="contacts[<?=$id?>][firstname]" autocomplete="given-name" <?=$this->valueOf($contact?->getFirstname())?> required>
                    <label for="contact-lastname-<?=$id?>">Nom</label>
                    <input id="contact-lastname-<?=$id?>" type="text" name="contacts[<?=$id?>][lastname]" autocomplete="family-name" <?=$this->valueOf($contact?->getLastname())?> required>
                    <label for="contact-role-<?=$id?>">Titre</label>
                    <input id="contact-role-<?=$id?>" type="text" name="contacts[<?=$id?>][role]" autocomplete="organization-title" <?=$this->valueOf($contact?->getRole())?> required>
                    <label for="contact-email-<?=$id?>">Email</label>
                    <input id="contact-email-<?=$id?>" type="text" name="contacts[<?=$id?>][email]" autocomplete="email" <?=$this->valueOf($contact?->getEmail())?> required>
                    <label for="contact-phone-<?=$id?>">Téléphone</label>
                    <select name="contacts[<?=$id?>][phoneCode]" id="contact-phone-code-<?=$id?>" autocomplete="tel-country-code">
                    <?php foreach ($this->phoneCodes as $codeCountry) : 
                        $code = substr($codeCountry, 0, strpos($codeCountry, ' '));?>
                        <option value="<?=$code;?>"<?=$this->selectPhoneCode($contact, $code)?>><?=$codeCountry?></option>
                    <?php endforeach; ?>
                    </select>
                    <input id="contact-phone-<?=$id?>" type="text" name="contacts[<?=$id?>][phoneNum]" autocomplete="tel-national" <?=$this->valueOf($contact?->getPhoneNum())?> required>
                </address>
                <?php if($removable) : ?>
                    <div id="rm-contact-<?=$id?>" class="rm-bt" data-type="ol"></div>
                <?php endif; ?>
            </fieldset>
        <?php
    }

    private function createCoorField($coor) {
        ?>
            <fieldset id="coor">
                <legend>Coordonnées</legend>
                <label for="lat">Latitude</label>
                <input id="lat" type="number" name="coor[lat]" min="41" max="52" step="0.00001" <?=$this->valueOf($coor?->getLat())?> required>
                <label for="lon">Longitude</label>
                <input id="lon" type="number" name="coor[lon]" min="-6" max="11" step="0.00001" <?=$this->valueOf($coor?->getLon())?> required>
            </fieldset>
        <?php
    }

    private function createMicroField($fieldId, $micro, bool $removable) {
        $id = $micro?->getId()??0;

        $model = $micro?->getModel();
        $controller = $micro?->getController();
        $brand = $model?->getBrand();
        $compagny = $brand?->getCompagny();
        ?>
        <fieldset id="micro-field-<?=$id?>" class="micro-field">
            <legend>Microscope n°<?=$fieldId + 1?></legend>
            <label for="micro-compagny-<?=$id?>">Société</label>
            <input id="micro-compagny-<?=$id?>" class="micro-compagy" list="micro-compagnies" name="micros[<?=$id?>][compagny]" <?=$this->valueOf($compagny?->getName())?> required>
            <label for="micro-brand-<?=$id?>">Marque</label>
            <input id="micro-brand-<?=$id?>" class="micro-brand" list="micro-brands-<?=$id?>" name="micros[<?=$id?>][brand]" <?=$this->valueOf($brand?->getName())?> required <?=isset($micro) ? "" : "disabled"?>>
            <datalist id="micro-brands-<?=$id?>">
            </datalist>
            <label for="micro-model-<?=$id?>">Modèle</label>
            <input id="micro-model-<?=$id?>" list="micro-models-<?=$id?>" name="micros[<?=$id?>][model]" <?=$this->valueOf($model?->getName())?> required <?=isset($micro) ? "" : "disabled"?>>
            <datalist id="micro-models-<?=$id?>">
            </datalist>
            <label for="micro-controller-<?=$id?>">Électronique / Contrôleur</label>
            <input id="micro-controller-<?=$id?>" list="micro-controllers-<?=$id?>" name="micros[<?=$id?>][controller]" <?=$this->valueOf($controller?->getName())?> required <?=isset($micro) ? "" : "disabled"?>>
            <datalist id="micro-controllers-<?=$id?>">
            </datalist>
            <label for="micro-type-<?=$id?>">Type</label>
            <select id="micro-type-<?=$id?>" name="micros[<?=$id?>][type]">
                <option value="LABO" <?=$micro?->getType() == "LABO" ? "selected" : ""?>>Laboratoire</option>
                <option value="PLAT" <?=$micro?->getType() == "PLAT" ? "selected" : ""?>>Plateforme</option>
            </select>
            <label for="micro-rate-<?=$id?>">Tarification (le cas échéant. Lien internet)</label>
            <input id="micro-rate-<?=$id?>" type="url" name="micros[<?=$id?>][rate]" <?=$this->valueOf($micro?->getRate())?> autocomplete="url">
            <label for="micro-access-<?=$id?>">Ouvert aux</label>
            <select name="micros[<?=$id?>][access]" id="micro-access-<?=$id?>">
                <option value="ACAD" <?=$micro?->getAccess() == "ACAD" ? "selected" : ""?>>Académiques</option>
                <option value="INDU" <?=$micro?->getAccess() == "INDU" ? "selected" : ""?>>Industriels</option>
                <option value="BOTH" <?=$micro?->getAccess() == "BOTH" ? "selected" : ""?>>Académiques et Industriels</option>
            </select>
            <label for="micro-desc-<?=$id?>">Description</label>
            <textarea id="micro-desc-<?=$id?>" name="micros[<?=$id?>][desc]" cols="30" rows="10" required><?=$micro?->getDesc()?></textarea>
            <div>
                <label for="micro-img-<?=$id?>">Photo</label>
                    <input id="micro-img-<?=$id?>" name="imgs[<?=$id?>]" type="file" accept="image/png, image/jpg, image/jpeg, image/webp">
                <?php 
                    if(isset($micro)) :
                        $microId = $micro->getId();
                        $name = implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $controller->getName()]);

                        $path = glob(__DIR__ . "/../../public/img/micros/" . "$microId.*");

                        if($path) :
                            if(browserSupportsWebp())
                                $extension = ".webp"; 
                            else
                                $extension = ".jpeg"; 
                ?>
                            <div>
                                <img class="micro-snapshot" src="/public/img/micros/<?=$microId . $extension?>" alt="Microscope <?=$name?>">
                                <div class="rm-bt"></div>
                                <input type="hidden" name="keepImg[<?=$microId?>]" value="true">
                            </div>
                <?php
                        endif; 
                    endif;
                ?>
            </div>
                
            <fieldset id="keywords">
                <legend>Mots-clés</legend>
                <?php 
                    $keyWordService = KeywordService::getInstance();
                    $cats = $keyWordService->getAllCategories();
                    foreach ($cats as $cat): 
                        $catName =$cat->getName();
                        $normCat = HTMLNormalize($catName)?>
                        <div>
                            <label for="cat-<?=$normCat?>-<?=$id?>"><?=$catName?></label>
                            <input id="cat-<?=$normCat?>-<?=$id?>" class="cat-input" list="cats-<?=$normCat?>">
                            <?php foreach (array_filter($micro?->getKeywords()??[], function ($kw) use ($catName) {
                                return $kw->getCat()->getName() == $catName;
                                }) as $kw): ?>
                                <div class="tag">
                                    <div class="rm-bt" data-type="ul"></div>
                                    <?=$kw->getTag()?>
                                    <input id="micro-kw-<?=HTMLNormalize($catName)?>-<?=$id?>" type="hidden" name="micros[<?=$id?>][keywords][<?=$catName?>][]" value="<?=$kw->getTag()?>">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
            </fieldset>
            <?php if($removable) : ?>
                <div class="rm-bt" data-type="ol" id="rm-micro-<?=$id?>"></div>
            <?php endif; ?>
        </fieldset>
        <?php
    }
}

    