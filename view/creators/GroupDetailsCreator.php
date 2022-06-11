<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/../../model/services/MicroscopesGroupService.php");
    include_once(__DIR__ . "/../../model/services/MicroscopeService.php");
    include_once(__DIR__ . "/Creator.php");

    Class GroupDetailsCreator implements Creator {
        public function __construct(private MicroscopesGroup $group, private bool $showMap, private $microId = null) {}

        private function createEditBts() {
            $groupId = $this->group->getId();
            if(isUserSessionValid() && ($_SESSION["user"]["id"] == MicroscopesGroupService::getInstance()->findGroupOwner($this->group)?->getId() || $_SESSION["user"]["admin"])):
            ?>
                <?php
                if($this->group->isLocked() == true && $_SESSION["user"]["admin"]): ?>
                    <form action="/processing/unlock_group_processing.php" method="GET">
                        <input type="hidden" name="groupId" value="<?=$groupId?>">
                        <input type="submit" value="Valider la fiche" class="bt">
                    </form>
                <?php endif; ?>
                <a href="/edit_micros_group.php?id=<?=$groupId?>"><div class="bt edit-bt"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M490.3 40.4C512.2 62.27 512.2 97.73 490.3 119.6L460.3 149.7L362.3 51.72L392.4 21.66C414.3-.2135 449.7-.2135 471.6 21.66L490.3 40.4zM172.4 241.7L339.7 74.34L437.7 172.3L270.3 339.6C264.2 345.8 256.7 350.4 248.4 353.2L159.6 382.8C150.1 385.6 141.5 383.4 135 376.1C128.6 370.5 126.4 361 129.2 352.4L158.8 263.6C161.6 255.3 166.2 247.8 172.4 241.7V241.7zM192 63.1C209.7 63.1 224 78.33 224 95.1C224 113.7 209.7 127.1 192 127.1H96C78.33 127.1 64 142.3 64 159.1V416C64 433.7 78.33 448 96 448H352C369.7 448 384 433.7 384 416V319.1C384 302.3 398.3 287.1 416 287.1C433.7 287.1 448 302.3 448 319.1V416C448 469 405 512 352 512H96C42.98 512 0 469 0 416V159.1C0 106.1 42.98 63.1 96 63.1H192z"/></svg></div></a>
                <form action="/processing/delete_group_processing.php" method="POST">
                    <input type="hidden" name="groupId" value="<?=$groupId?>">
                    <div class="bt rm-bt"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--! Font Awesome Pro 6.1.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2022 Fonticons, Inc. --><path d="M135.2 17.69C140.6 6.848 151.7 0 163.8 0H284.2C296.3 0 307.4 6.848 312.8 17.69L320 32H416C433.7 32 448 46.33 448 64C448 81.67 433.7 96 416 96H32C14.33 96 0 81.67 0 64C0 46.33 14.33 32 32 32H128L135.2 17.69zM31.1 128H416V448C416 483.3 387.3 512 352 512H95.1C60.65 512 31.1 483.3 31.1 448V128zM111.1 208V432C111.1 440.8 119.2 448 127.1 448C136.8 448 143.1 440.8 143.1 432V208C143.1 199.2 136.8 192 127.1 192C119.2 192 111.1 199.2 111.1 208zM207.1 208V432C207.1 440.8 215.2 448 223.1 448C232.8 448 240 440.8 240 432V208C240 199.2 232.8 192 223.1 192C215.2 192 207.1 199.2 207.1 208zM304 208V432C304 440.8 311.2 448 320 448C328.8 448 336 440.8 336 432V208C336 199.2 328.8 192 320 192C311.2 192 304 199.2 304 208z"/></svg></div>
                </form>
            <?php
            endif;
        }

        public function create() {
            ?>
            <section class="group-details">
                <section>
                    <div class="group-details-header">
                        <?php 
                            $lab = $this->group->getLab();
                            $labName = $lab->getName();
                            if($lab->getType() != "Autre")
                            $labName .= " (" . $this->group->getLab()->getType() . $this->group->getLab()->getCode() . ")"; 
                        ?> 
                        <h2><?= $labName ?></h2>
                        <?php $this->createEditBts() ?>
                    </div>
                    <?php if($this->showMap) : ?>
                        <div id="map-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="def">            
                                <defs>
                                    <path id="marker" d="M168.3 499.2C116.1 435 0 279.4 0 192C0 85.96 85.96 0 192 0C298 0 384 85.96 384 192C384 279.4 267 435 215.7 499.2C203.4 514.5 180.6 514.5 168.3 499.2H168.3zM192 256C227.3 256 256 227.3 256 192C256 156.7 227.3 128 192 128C156.7 128 128 156.7 128 192C128 227.3 156.7 256 192 256z"/>
                                </defs>
                            </svg>
                            <div id="map"></div>
                        </div>
                    <?php else: ?>
                        <p>Coordonnées : <?= $this->group->getCoor()->getLat() . ", " . $this->group->getCoor()->getLon(); ?></p>
                    <?php endif; ?>
                    <p><?= nl2br($this->group->getLab()->getAddress()->toString()); ?></p>
                    <p>Site internet : <a href="<?= $this->group->getLab()->getWebsite(); ?>" target="_blank"><?= $this->group->getLab()->getWebsite(); ?></a></p>
                </section>
                <section>
                    <h2>Référent·e·s</h2>
                    <div class="contacts-wrapper">
                        <?php $nb = 1; foreach ($this->group->getContacts() as $id => $contact) : ?>
                            <address class="contact-wrapper">
                                <h3>Référent·e n° <?=$nb++?></h3>
                                <p><?= $contact->getFirstname() . ' ' . $contact->getLastname() . " (" . $contact->getRole() .")" ?></p>
                                <p>Email : <a href="mailto:<?= $contact->getEmail() ?>"><?= $contact->getEmail() ?></a></p>
                                <?php $phone = $contact->getPhoneCode() . $contact->getPhoneNum(); ?>
                                <p>Téléphone : <a href="tel:<?= $phone ?>"><?= $phone ?></a></p>
                            </address>
                        <?php endforeach ?>
                    </div>
                </section>
                <section>
                    <?php $micros = $this->group->getMicroscopes(); ?>
                    <h2>Microscope<?= sizeof($micros) > 1 && $this->microId === null ? "s" : ""?></h2>
                    <?php 
                        $id = 1; foreach ($this->group->getMicroscopes() as $micro) :
                        if($this->microId !== null && $micro->getId() != $this->microId) 
                            continue;
                            
                        $ctr = $micro->getController();
                        $model = $micro->getModel();
                        $brand = $model->getBrand();
                        $compagny = $brand->getCompagny();
                        $type = match ($micro->getType()) {
                            "LABO" => "laboratoire",
                            "PLAT" => "plateforme"
                        };
                        if($compagny->getName() == "Homemade")
                            $name = "Homemade - " . $ctr->getName();
                        else
                            $name = implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $ctr->getName()]);

                        $imgPath = MicroscopeService::getInstance()->getImgPathById($micro->getId());
                        ?>
                        <section class="micro-section">
                            <h3><?= sizeof($micros) > 1 && $this->microId === null ? "Microscope n°{$id} - " : ""?> <?= $name . " (" . $type . ")"; ?></h3>
                            <img class="micro-img" src="<?=$imgPath?>" alt="Microscope <?=$name?>">
                            <div>
                                <p>Description : <?= $micro->getDescr(); ?></p>
                                <?php if(!empty($micro->getRate())) : ?>
                                    <p>Tarification : <a href="<?= $micro->getRate(); ?>" target="_blank"><?= $micro->getRate(); ?></a></p>
                                <?php endif; ?>
                                <?php 
                                $access = $micro->getAccess();
                                if($access == "BOTH" || $access == "ACAD") : ?>
                                    <p>Ouvert aux académiques</p>
                                <?php endif;
                                if($access == "BOTH" || $access == "INDU") : ?>
                                    <p>Ouvert aux industriels</p>
                                <?php endif; ?>
                            </div>
                            <div class="table-wrapper">
                                <table>
                                    <caption><h4>Mots-clés</h4></caption>
                                    <thead>
                                        <tr>
                                            <th scope="colgroup">Catégories</th>
                                            <th scope="colgroup">Étiquettes</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        foreach ($micro->getKeywords() as $kw)
                                            $cats[$kw->getCat()->getName()][] = $kw->getTag();
                                        
                                        foreach($cats??["Aucunes" => ["Aucunes"]] as $cat => $tags):
                                        ?>
                                            <tr>
                                                <th scope="rowgroup"><?= $cat; ?></th>
                                                <td><?= implode(", ", $tags); ?></td>
                                            </tr>
                                        <?php endforeach; unset($cats);?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                    <?php $id++;
                        endforeach ?>
                </section>
            </section>
            <?php
        }
    }