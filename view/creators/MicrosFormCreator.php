<?php
    include_once(__DIR__ . "/../../config/config.php");
    include_once(__DIR__ . "/FormCreator.php");
    include_once(__DIR__ . "/../../model/services/ControllerService.php");
    include_once(__DIR__ . "/../../model/services/ModelService.php");

    Class MicrosFormCreator extends FormCreator {
        function __construct() {
            parent::__construct("/processing/micros_form_processing.php", "POST", bigForm:true);
        }

        private function getNextId($entities) {
            $ids = array_keys($entities); 
            return empty($ids) ? 1 : max($ids) + 1;
        }

        public function createBody() {            
            $compagnyService =  CompagnyService::getInstance();
            $brandService =  BrandService::getInstance();
            $modelService =  ModelService::getInstance();
            $controllerService =  ControllerService::getInstance();

            $cmps = $compagnyService->findAllCompagnies(); ?>
            <h3>Sociétés</h3>
            <div id="cmps-wrapper" data-next-cmp-id="<?= $this->getNextId($cmps); ?>">
                <?php 
                foreach($cmps as $cmpId => $cmp): 
                    if($cmp->getName() == "Homemade") continue;
                    $brands = $brandService->findAllBrands($cmp); ?>
                    <div class="cmp-wrapper">
                        <input type="text" name="cmps[<?=$cmpId?>]" value="<?=$cmp->getName()?>">
                        <div class="rm-bt"></div>
                        <h4>Marques</h4>
                        <div class="brands-wrapper" data-next-brand-id="<?= $this->getNextId($brands); ?>" data-parent-id=<?=$cmpId?>>
                            <?php
                            foreach($brands as $brandId => $brand): 
                                $models = $modelService->findAllModels($brand);
                                $ctrs = $controllerService->findAllControllers($brand); ?>
                                <div class="brand-wrapper">
                                    <input type="text" name="brands[<?=$cmpId?>][<?=$brandId?>]" value="<?=$brand->getName()?>">
                                    <div class="rm-bt"></div>
                                    <h5>Modèles</h5>
                                    <div class="models-wrapper" data-next-model-id="<?= $this->getNextId($models); ?>" data-parent-id=<?=$brandId?>>
                                        <?php
                                            foreach($models as $modelId => $model): ?>
                                            <div class="model-input-wrapper">
                                                <input type="text" name="models[<?=$cmpId?>][<?=$brandId?>][<?=$modelId?>]" value="<?=$model->getName()?>">
                                                <div class="rm-bt"></div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div id="add-model" class="add-bt"></div>
                                    </div>
                                    <h5>Électroniques / Contrôleurs</h5>
                                    <div class="ctrs-wrapper" data-next-ctr-id="<?= $this->getNextId($ctrs); ?>" data-parent-id=<?=$brandId?>>
                                        <?php
                                            foreach($ctrs as $ctrId => $ctr): ?>
                                            <div class="ctr-input-wrapper">
                                                <input type="text" name="ctrs[<?=$cmpId?>][<?=$brandId?>][<?=$ctrId?>]" value="<?=$ctr->getName()?>">
                                                <div class="rm-bt"></div>
                                            </div>
                                        <?php endforeach; ?>
                                        <div id="add-ctr" class="add-bt"></div>
                                    </div>
                                </div>
                            <?php
                            endforeach; 
                            ?>
                            <div id="add-brand" class="add-bt"></div>
                        </div>
                    </div>
                <?php
                endforeach; ?>
                <div id="add-cmp" class="add-bt"></div>
            </div>
            <input type="submit" class="bt">
            <?php
        }
    }