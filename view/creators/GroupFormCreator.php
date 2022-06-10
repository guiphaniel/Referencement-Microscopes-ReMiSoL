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
            parent::__construct("processing/group_form_processing.php", "post", "multipart/form-data", bigForm:true);
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
                        $first = false;
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
                            <input id="lab-code" type="number" name="lab[code]" min="10" max="9999" <?=$this->valueOf($lab?->getCode())?> placeholder=" " <?=isset($lab) && $lab->getCode() == null ? "disabled" : ""?> required>
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
                        <input id="contact-lastname-<?=$id?>" class="strtoupper" type="text" name="contacts[<?=$id?>][lastname]" autocomplete="family-name" <?=$this->valueOf($contact?->getLastname())?> placeholder=" " required>
                        <label for="contact-lastname-<?=$id?>">Nom</label>
                    </div>
                    <div class="input-wrapper">
                        <input id="contact-role-<?=$id?>" class="ucfirst" type="text" name="contacts[<?=$id?>][role]" autocomplete="organization-title" <?=$this->valueOf($contact?->getRole())?> placeholder=" " required>
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
                    <div id="rm-contact-<?=$id?>" class="bt rm-bt" data-type="ol">Supprimer la·le référent·e</div>
                <?php endif; ?>
            </fieldset>
        <?php
    }

    private function createCoorField($coor) {
        ?>
            <fieldset id="coor">
                <legend><h2>Coordonnées<span class="tooltip" data-tooltip-content="Renseignez les coordonnées de votre groupe de microscope. Il vous suffit de cliquer sur la carte à l'emplacement désiré !"></span></h2></legend>
                <div id="map-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="def">            
                        <defs>
                            <path id="marker" d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z"/>
                        </defs>
                    </svg>
                    <div id="map"></div>
                </div>
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

        $microModel = $micro?->getModel();
        $microController = $micro?->getController();
        $microBrand = $microModel?->getBrand();
        $microCompagny = $microBrand?->getCompagny();
        ?>
        <fieldset id="micro-field-<?=$id?>" class="micro-field">
            <legend><h3>Microscope n°<?=$fieldId?></h3></legend>
            <div class="select-wrapper">
                <label for="micro-compagnies-<?=$id?>">Société</label>
                <select id="micro-compagnies-<?=$id?>" class="micro-compagnies" name="micros[<?=$id?>][compagny]" required>
                    <option value="" <?= !isset($micro) ? "selected" : "" ?> disabled hidden>Choisissez ici</option>
                    <?php foreach (CompagnyService::getInstance()->findAllCompagnies() as $cmp): ?>
                        <option value="<?=$cmp->getName()?>" <?=$microCompagny?->getName() === $cmp->getName() ? "selected" : ""?>><?=$cmp->getName()?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-brands-<?=$id?>">Marque</label>
                <select id="micro-brands-<?=$id?>" class="micro-brands" name="micros[<?=$id?>][brand]" required <?=isset($micro) ? "" : "disabled"?>>
                    <option value="" <?php !isset($micro) ? "selected" : "" ?> disabled hidden>Choisissez ici</option>
                    <?php 
                    if(isset($micro)): 
                        foreach (BrandService::getInstance()->findAllBrands($microCompagny) as $brand):?>
                            <option value="<?=$brand->getName()?>" <?=$microBrand->getName() === $brand->getName() ? "selected" : ""?>><?=$brand->getName()?></option>
                    <?php 
                        endforeach;                        
                    endif; ?>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-models-<?=$id?>">Modèle</label>
                <select id="micro-models-<?=$id?>" name="micros[<?=$id?>][model]" required <?=isset($micro) ? "" : "disabled"?>>
                    <option value="" <?php !isset($micro) ? "selected" : "" ?> disabled hidden>Choisissez ici</option>
                    <?php 
                    if(isset($micro)): 
                        foreach (ModelService::getInstance()->findAllModels($microBrand) as $model):?>
                            <option value="<?=$model->getName()?>" <?=$microModel->getName() === $model->getName() ? "selected" : ""?>><?=$model->getName()?></option>
                    <?php 
                        endforeach;                        
                    endif; ?>
                </select>
            </div>
            <div class="select-wrapper">
                <label for="micro-controllers-<?=$id?>">Électronique / Contrôleur</label>
                <select id="micro-controllers-<?=$id?>" name="micros[<?=$id?>][controller]" required <?=isset($micro) ? "" : "disabled"?>>
                    <option value="" <?php !isset($micro) ? "selected" : "" ?> disabled hidden>Choisissez ici</option>
                    <?php 
                    if(isset($micro)): 
                        foreach (ControllerService::getInstance()->findAllControllers($microBrand) as $ctr):?>
                            <option value="<?=$ctr->getName()?>" <?=$microController->getName() === $ctr->getName() ? "selected" : ""?>><?=$ctr->getName()?></option>
                    <?php 
                        endforeach;                        
                    endif; ?>
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
                <label for="micro-rate-<?=$id?>">Tarification<span class="tooltip" data-tooltip-content="Si vous proposez une tarification pour votre matériel, merci de fournir un lien internet vers celle-ci."></span></label>
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
                        $name = implode(" - ", [$microCompagny->getName(), $microBrand->getName(), $microModel->getName(), $microController->getName()]);

                        $path = glob(__DIR__ . "/../../public/img/micros/" . "$microId.*");

                        if($path) :
                            if(browserSupportsWebp())
                                $extension = ".webp"; 
                            else
                                $extension = ".jpeg"; 
                ?>
                            <div class="snapshot-wrapper">
                                <img class="micro-snapshot" src="/public/img/micros/<?=$microId . $extension?>" alt="Microscope <?=$name?>">
                                <div class="bt rm-bt"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg></div>
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
                <legend><h4>Mots-clés<span class="tooltip" data-tooltip-content="Choisissez des mots-clés parmis ceux proposés. Notez que sur certains navigateurs, il peut être nécessaire de cliquer deux fois sur le champ pour que la liste s'affiche."></span></h4></legend>
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
                        <div class="tags-wrapper">
                        <?php foreach (array_filter($micro?->getKeywords()??[], function ($kw) use ($catName) {
                            return $kw->getCat()->getName() == $catName;
                        }) as $kw): ?>
                            <div class="tag">
                                <div class="bt rm-bt" data-type="ul"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg></div>
                                <?=$kw->getTag()?>
                                <input id="micro-kw-<?=strNormalize($catName)?>-<?=$id?>" type="hidden" name="micros[<?=$id?>][keywords][<?=$catName?>][]" value="<?=$kw->getTag()?>">
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
            </fieldset>
            <?php if($removable) : ?>
                <div class="bt rm-bt" data-type="ol" id="rm-micro-<?=$id?>">Supprimer le microscope</div>
            <?php endif; ?>
        </fieldset>
        <?php
    }
}

    