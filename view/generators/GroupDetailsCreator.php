<?php
    include_once(__DIR__ . "/../../include/config.php");
    include_once(__DIR__ . "/../../utils/browser_supports_webp.php");
    include_once(__DIR__ . "/../../model/services/MicroscopesGroupService.php");
    include_once(__DIR__ . "/Creator.php");

    Class GroupDetailsCreator implements Creator {
        public function __construct(private MicroscopesGroup $group, private bool $showMap) {}

        private function createEditBts() {
            $groupId = $this->group->getId();
            if($_SESSION["user"]["id"] == MicroscopesGroupService::getInstance()->findGroupOwner($this->group)?->getId() || $_SESSION["user"]["admin"])
            ?>
                <div class="rm-bt"></div>
                <a href="/edit_micros_group.php?id=<?=$groupId?>"><div class="edit-bt"></div></a>
            <?php
        }

        public function create() {
            ?>
                <section>
                    <div>
                        <h2><?= $this->group->getLab()->getName() . " (" . $this->group->getLab()->getType() . $this->group->getLab()->getCode() . ")"; ?></h2>
                        <?php $this->createEditBts() ?>
                    </div>
                    <?php if($this->showMap) : ?>
                        <div id="map" style="height: 400px;"></div>
                    <?php else: ?>
                        <p>Coordonnées : <?= $this->group->getCoor()->getLat() . ", " . $this->group->getCoor()->getLon(); ?></p>
                    <?php endif; ?>
                    <p><?= nl2br($this->group->getLab()->getAddress()->toString()); ?></p>
                    <p>Site internet : <a href="<?= $this->group->getLab()->getWebsite(); ?>"><?= $this->group->getLab()->getWebsite(); ?></a></p>
                </section>
                <section>
                    <h2>Référent·e·s</h2>
                    <?php foreach ($this->group->getContacts() as $contact) : ?>
                        <address>
                            <p><?= $contact->getFirstname() . ' ' . $contact->getLastname() . " (" . $contact->getRole() .")" ?></p>
                            <p>Email : <a href="mailto:<?= $contact->getEmail() ?>"><?= $contact->getEmail() ?></a></p>
                            <?php $phone = $contact->getPhoneCode() . $contact->getPhoneNum(); ?>
                            <p>Téléphone : <a href="tel:<?= $phone ?>"><?= $phone ?></a></p>
                        </address>
                    <?php endforeach ?>
                </section>
                <section>
                    <h2>Microscopes</h2>
                    <?php foreach ($this->group->getMicroscopes() as $micro) : 
                        $ctr = $micro->getController();
                        $model = $micro->getModel();
                        $brand = $model->getBrand();
                        $compagny = $brand->getCompagny();
                        $type = match ($micro->getType()) {
                            "LABO" => "laboratoire",
                            "PLAT" => "plateforme"
                        };
                        $name = implode(" - ", [$compagny->getName(), $brand->getName(), $model->getName(), $ctr->getName()]);
                        ?>
                        <section>
                            <h3><?= $name . " (" . $type . ")"; ?></h3>
                            <?php 
                                $microId = $micro->getId();

                                $path = glob(__DIR__ . "/../../public/img/micros/" . "$microId.*");

                                if($path) :
                                    if(browserSupportsWebp())
                                        $extension = ".webp"; 
                                    else
                                        $extension = ".jpeg"; 
                            ?>
                                <img class="micro-img" src="/public/img/micros/<?=$microId . $extension?>" alt="Microscope <?=$name?>">
                            <?php endif; ?>
                            <p>Description : <?= $micro->getDesc(); ?></p>
                            <?php if(!empty($micro->getRate())) : ?>
                                <p>Tarification : <a href="<?= $micro->getRate(); ?>"><?= $micro->getRate(); ?></a></p>
                            <?php endif; ?>
                            <?php 
                            $access = $micro->getAccess();
                            if($access == "BOTH" || $access == "ACAD") : ?>
                                <p>Ouvert aux académiques</p>
                            <?php endif;
                            if($access == "BOTH" || $access == "INDU") : ?>
                                <p>Ouvert aux industriels</p>
                            <?php endif; ?>
                            <table>
                                <caption>Mots-clés</caption>
                                <thead>
                                    <tr>
                                        <th scope="colgroup">Catégories</th>
                                        <th scope="colgroup">Étiquettes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($micro->getKeywords() as $cat => $tags) : ?>
                                        <tr>
                                            <th scope="rowgroup"><?= $cat; ?></th>
                                            <td><?= implode(", ", $tags); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </section>
                    <?php endforeach ?>
                </section>
            <?php
        }
    }