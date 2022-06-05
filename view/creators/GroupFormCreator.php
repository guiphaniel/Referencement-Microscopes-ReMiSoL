<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/../../utils/browser_supports_webp.php");
    include_once(__DIR__ . "/FormCreator.php");
    include_once(__DIR__ . "/../../model/services/MicroscopesGroupService.php");
    include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

    Class GroupFormCreator extends FormCreator {
        private $labTypes = ["EMR", "FR", "IRL", "UAR", "UMR", "UPR", "Autre"];
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
                <legend><h2>Référent·e·s</h2></legend>
                <?php
                //create contact fields (only the first one isn't removable)
                $first = true;  
                $contactFieldId = 1;
                foreach ($this->group?->getContacts()??[1 => null] as $contact) {
                    $this->createContactField($contactFieldId++, $contact, !$first);
                    
                    if($first)
                        continue;
                } 
                ?>
                <div id="add-contact" class="bt add-bt">Ajouter un·e référent·e</div>
            </fieldset>
            <?php
            $this->createCoorField($this->group?->getCoor());
            ?>

            <fieldset id="micros">
                <legend><h2>Microscopes</h2></legend>
                <!-- Keywords datalists -->
                <?php 
                    $keyWordService = KeywordService::getInstance();
                    $cats = $keyWordService->findAllCategories();
                    foreach ($cats as $cat): 
                        $catName = $cat->getName();
                        echo "<!-- $catName datalist -->" ?>
                        <datalist id="cats-<?=strNormalize($catName)?>">
                        <?php 
                            $tags = $keyWordService->findAllTags($cat);
                            foreach ($tags as $tag): ?>
                                <option value="<?=$tag?>">
                            <?php endforeach; ?>
                        </datalist>
                    <?php endforeach; 
                    
                    //create all micro fields (only the first one isn't removable)
                    $first = true;  
                    $microFieldId = 1;
                    foreach ($this->group?->getMicroscopes()??[1 => null] as $micro) {
                        $this->createMicroField($microFieldId++, $micro, !$first);

                        if($first)
                            $first = false;
                    }  
                ?>
                <div id="add-micro" class="bt add-bt">Ajouter un microscope</div>
            </fieldset>
            <input class="bt" type="submit">
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
                <legend><h2>Laboratoire / service</h2></legend>
                <address>
                    <div class="input-wrapper">
                        <input id="lab-name" type="text" name="lab[name]" autocomplete="organization" <?=$this->valueOf($lab?->getName())?> placeholder=" " required>
                        <label for="lab-name">Nom du laboratoire</label>
                    </div>
                    <div class="select-input">
                        <select name="lab[type]" id="lab-type" required>
                            <option value="">Selectionnez un acronyme</option>
                            <?php foreach ($this->labTypes as $labType) : ?>
                                <option value=<?=$labType;?> <?= $lab?->getType() == $labType ? "selected" : "" ?>><?=$labType?></option>
                            <?php endforeach; ?>
                        </select>
                        <div class="input-wrapper">
                            <input id="lab-code" type="number" name="lab[code]" min="10" max="9999" <?=$this->valueOf($lab?->getCode())?> placeholder=" " required>
                            <label for="lab-code">Code</label>
                        </div>
                    </div>
                    <div class="input-wrapper">
                        <input id="lab-address-school" type="text" name="lab[address][school]" <?=$this->valueOf($lab?->getAddress()->getSchool())?> placeholder=" ">
                        <label for="lab-address-school">Université / École</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="lab-address-street" type="text" name="lab[address][street]" autocomplete="address-line1" <?=$this->valueOf($lab?->getAddress()->getStreet())?> placeholder=" " required>
                        <label for="lab-address-street">Adresse</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="lab-address-zip" type="text" name="lab[address][zipCode]" autocomplete="postal-code" <?=$this->valueOf($lab?->getAddress()->getZipCode())?> placeholder=" " required>
                        <label for="lab-address-zip">Code postal</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="lab-address-city" type="text" name="lab[address][city]" autocomplete="address-level2" <?=$this->valueOf($lab?->getAddress()->getCity())?> placeholder=" " required>
                        <label for="lab-address-city">Ville</label>
                    </div>
                    <div class="select-wrapper">
                        <label for="lab-address-country">Pays</label>
                        <select name="lab[address][country]" id="lab-address-country" autocomplete="country">
                            <?php foreach ($this->countries as $country) : ?>
                                <option value="<?=$country;?>" <?=$this->selectCountry($country)?>><?=$country?></option>
                                <?php endforeach; ?>
                            </select>
                    </div>
                    <div class="input-wrapper">
                        <input id="lab-website" type="url" name="lab[website]" autocomplete="url" <?=$this->valueOf($lab?->getWebsite())?> placeholder=" " required>
                        <label for="lab-website">Site web</label>
                    </div>
                </address>
            </fieldset>   
        <?php
    }

    private function createContactField($fieldId, $contact, bool $removable) {
        $id = $contact?->getId()??$fieldId;
        ?>
            <fieldset id="contact-field-<?=$id?>" class="contact-field">
                <legend><h3>Référent·e n°<?=$fieldId?></h3></legend>
                <address>
                    <div class="input-wrapper">
                        <input id="contact-firstname-<?=$id?>" type="text" name="contacts[<?=$id?>][firstname]" autocomplete="given-name" <?=$this->valueOf($contact?->getFirstname())?> placeholder=" " required>
                        <label for="contact-firstname-<?=$id?>">Prénom</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="contact-lastname-<?=$id?>" type="text" name="contacts[<?=$id?>][lastname]" autocomplete="family-name" <?=$this->valueOf($contact?->getLastname())?> placeholder=" " required>
                        <label for="contact-lastname-<?=$id?>">Nom</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="contact-role-<?=$id?>" type="text" name="contacts[<?=$id?>][role]" autocomplete="organization-title" <?=$this->valueOf($contact?->getRole())?> placeholder=" " required>
                        <label for="contact-role-<?=$id?>">Titre</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="contact-email-<?=$id?>" type="text" name="contacts[<?=$id?>][email]" autocomplete="email" <?=$this->valueOf($contact?->getEmail())?> placeholder=" " required>
                        <label for="contact-email-<?=$id?>">Email</label>
                    </div>
                    <div class="select-input">
                        <select name="contacts[<?=$id?>][phoneCode]" id="contact-phone-code-<?=$id?>" autocomplete="tel-country-code">
                            <?php foreach ($this->phoneCodes as $codeCountry) : 
                                $code = substr($codeCountry, 0, strpos($codeCountry, ' '));?>
                                <option value="<?=$code;?>" <?=$this->selectPhoneCode($contact, $code)?>><?=$codeCountry?></option>
                                <?php endforeach; ?>
                        </select>
                        <div class="input-wrapper">
                            <input id="contact-phone-<?=$id?>" type="text" name="contacts[<?=$id?>][phoneNum]" autocomplete="tel-national" <?=$this->valueOf($contact?->getPhoneNum())?> placeholder=" " required>
                            <label for="contact-phone-<?=$id?>">Téléphone</label>
                        </div>
                    </div>
                </address>
                <?php if($removable) : ?>
                    <div id="rm-contact-<?=$id?>" class="bt rm-bt" data-type="ol"></div>
                <?php endif; ?>
            </fieldset>
        <?php
    }

    private function createCoorField($coor) {
        ?>
            <fieldset id="coor">
                <legend><h2>Coordonnées</h2></legend>
                <div class="input-wrapper">
                    <input id="lat" type="number" name="coor[lat]" min="41" max="52" step="0.00001" <?=$this->valueOf($coor?->getLat())?> placeholder=" " required>
                    <label for="lat">Latitude</label>
                </div>
                <div class="input-wrapper">
                    <input id="lon" type="number" name="coor[lon]" min="-6" max="11" step="0.00001" <?=$this->valueOf($coor?->getLon())?> placeholder=" " required>
                    <label for="lon">Longitude</label>
                </div>
            </fieldset>
        <?php
    }

    private function createMicroField($fieldId, $micro, bool $removable) {
        $id = $micro?->getId()??$fieldId;

        $model = $micro?->getModel();
        $controller = $micro?->getController();
        $brand = $model?->getBrand();
        $compagny = $brand?->getCompagny();
        ?>
        <fieldset id="micro-field-<?=$id?>" class="micro-field">
            <legend><h3>Microscope n°<?=$fieldId?></h3></legend>
            <div class="select-wrapper">
                <label for="micro-compagnies-<?=$id?>">Société</label>
                <select id="micro-compagnies-<?=$id?>" class="micro-compagnies" name="micros[<?=$id?>][compagny]" <?=$this->valueOf($compagny?->getName())?> required>
                <option value="" selected disabled hidden>Choisissez ici</option>
                    <?php foreach (CompagnyService::getInstance()->findAllCompagnies() as $compagny): ?>
                        <option value="<?=$compagny->getName()?>"><?=$compagny->getName()?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-brands-<?=$id?>">Marque</label>
                <select id="micro-brands-<?=$id?>" class="micro-brands" name="micros[<?=$id?>][brand]" <?=$this->valueOf($brand?->getName())?> required <?=isset($micro) ? "" : "disabled"?>>
                    <option value="" selected disabled hidden>Choisissez ici</option>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-models-<?=$id?>">Modèle</label>
                <select id="micro-models-<?=$id?>" name="micros[<?=$id?>][model]" <?=$this->valueOf($model?->getName())?> required <?=isset($micro) ? "" : "disabled"?>>
                    <option value="" selected disabled hidden>Choisissez ici</option>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-controllers-<?=$id?>">Électronique / Contrôleur</label>
                <select id="micro-controllers-<?=$id?>" name="micros[<?=$id?>][controller]" <?=$this->valueOf($controller?->getName())?> required <?=isset($micro) ? "" : "disabled"?>>
                    <option value="" selected disabled hidden>Choisissez ici</option>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-type-<?=$id?>">Type</label>
                <select id="micro-type-<?=$id?>" name="micros[<?=$id?>][type]">
                    <option value="LABO" <?=$micro?->getType() == "LABO" ? "selected" : ""?>>Laboratoire</option>
                    <option value="PLAT" <?=$micro?->getType() == "PLAT" ? "selected" : ""?>>Plateforme</option>
                </select>
            </div>
            <div class="input-wrapper">
                <input id="micro-rate-<?=$id?>" type="url" name="micros[<?=$id?>][rate]" <?=$this->valueOf($micro?->getRate())?> autocomplete="url" placeholder=" ">
                <label for="micro-rate-<?=$id?>">Tarification (si concerné : lien internet)</label>
            </div>
            <div class="select-wrapper">
                <label for="micro-access-<?=$id?>">Ouvert aux</label>
                <select name="micros[<?=$id?>][access]" id="micro-access-<?=$id?>">
                    <option value="ACAD" <?=$micro?->getAccess() == "ACAD" ? "selected" : ""?>>Académiques</option>
                    <option value="INDU" <?=$micro?->getAccess() == "INDU" ? "selected" : ""?>>Industriels</option>
                    <option value="BOTH" <?=$micro?->getAccess() == "BOTH" ? "selected" : ""?>>Académiques et Industriels</option>
                </select>
            </div>
            <div class="input-wrapper">
                <textarea id="micro-descr-<?=$id?>" name="micros[<?=$id?>][descr]" maxlength="2000" cols="30" rows="10" placeholder=" " required><?=$micro?->getDescr()?></textarea>
                <label for="micro-descr-<?=$id?>">Description (2000 caractères max.)</label>
            </div>
            <fieldset>
            <legend><h4>Image</h4></legend>
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
                            <div class="snapshot-wrapper">
                                <img class="micro-snapshot" src="/public/img/micros/<?=$microId . $extension?>" alt="Microscope <?=$name?>">
                                <div class="bt rm-bt"></div>
                                <input type="hidden" name="keepImg[<?=$microId?>]" value="true">
                            </div>
                            <input id="micro-img-<?=$id?>" name="imgs[<?=$id?>]" type="file" accept="image/png, image/jpg, image/jpeg, image/webp">
                            <label for="micro-img-<?=$id?>" class="bt edit-bt">Modifier l'image</label>
                <?php
                        else: ?>
                            <input id="micro-img-<?=$id?>" name="imgs[<?=$id?>]" type="file" accept="image/png, image/jpg, image/jpeg, image/webp">
                            <label for="micro-img-<?=$id?>" class="bt add-bt">Ajouter une image</label>
                <?php 
                        endif;
                    else: ?>
                        <input id="micro-img-<?=$id?>" name="imgs[<?=$id?>]" type="file" accept="image/png, image/jpg, image/jpeg, image/webp">
                        <label for="micro-img-<?=$id?>" class="bt add-bt">Ajouter une image</label>
                <?php
                    endif;
                ?>
            </fieldset>
                
            <fieldset id="keywords">
                <legend><h4>Mots-clés</h4></legend>
                <?php 
                    $keyWordService = KeywordService::getInstance();
                    $cats = $keyWordService->findAllCategories();
                    foreach ($cats as $cat): 
                        $catName =$cat->getName();
                        $normCat = strNormalize($catName)?>
                        <div class="input-wrapper">
                            <input id="cat-<?=$normCat?>-<?=$id?>" class="cat-input" list="cats-<?=$normCat?>" placeholder=" ">
                            <label for="cat-<?=$normCat?>-<?=$id?>"><?=$catName?></label>
                        </div>
                        <?php foreach (array_filter($micro?->getKeywords()??[], function ($kw) use ($catName) {
                            return $kw->getCat()->getName() == $catName;
                            }) as $kw): ?>
                            <div class="tag">
                                <div class="bt rm-bt" data-type="ul"></div>
                                <?=$kw->getTag()?>
                                <input id="micro-kw-<?=strNormalize($catName)?>-<?=$id?>" type="hidden" name="micros[<?=$id?>][keywords][<?=$catName?>][]" value="<?=$kw->getTag()?>">
                            </div>
                        <?php endforeach;
                    endforeach; ?>
            </fieldset>
            <?php if($removable) : ?>
                <div class="bt rm-bt" data-type="ol" id="rm-micro-<?=$id?>"></div>
            <?php endif; ?>
        </fieldset>
        <?php
    }
}

    