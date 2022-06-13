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
            <div id="cmps-wrapper" class="wrapper" data-next-cmp-id="<?= $this->getNextId($cmps); ?>">
                <h4>Sociétés</h4> 
                <?php 
                foreach($cmps as $cmpId => $cmp): 
                    if($cmp->getName() == "Homemade") continue;
                    $brands = $brandService->findAllBrands($cmp); ?>
                    <fieldset>
                        <?php $this->createInput("cmp-$cmpId", "cmps[$cmpId]", "Société", $cmp->getName(), class:"ucfirst") ?>
                        <fieldset class="brands-wrapper wrapper" data-next-brand-id="<?= $this->getNextId($brands); ?>" data-parent-id=<?=$cmpId?>>
                            <h4>Marques</h4>  
                            <?php
                            foreach($brands as $brandId => $brand): 
                                $models = $modelService->findAllModels($brand);
                                $ctrs = $controllerService->findAllControllers($brand); ?>
                                <fieldset>
                                    <?php $this->createInput("brand-$cmpId-$brandId", "brands[$cmpId][$brandId]", "Marque", $brand->getName(), class:"ucfirst") ?>
                                    <fieldset class="models-wrapper" data-next-model-id="<?= $this->getNextId($models); ?>" data-parent-id=<?=$brandId?>>
                                        <h5>Modèles</h5>    
                                        <?php foreach($models as $modelId => $model): ?>
                                            <?php $this->createInputRm("model-$cmpId-$brandId-$modelId", "models[$cmpId][$brandId][$modelId]", "Modèle", $model->getName(), class:"ucfirst") ?>
                                        <?php endforeach; ?>
                                        <div id="add-model" class="bt add-bt">Ajouter un modèle</div>
                                    </fieldset>
                                    <fieldset class="ctrs-wrapper" data-next-ctr-id="<?= $this->getNextId($ctrs); ?>" data-parent-id=<?=$brandId?>>
                                        <h5>Contrôleurs</h5>
                                        <?php
                                            foreach($ctrs as $ctrId => $ctr): ?>
                                            <?php $this->createInputRm("ctr-$cmpId-$brandId-$ctrId", "ctrs[$cmpId][$brandId][$ctrId]", "Contrôleur", $ctr->getName(), class:"ucfirst") ?>
                                        <?php endforeach; ?>
                                        <div id="add-ctr" class="bt add-bt">Ajouter un contrôleur</div>
                                    </fieldset>
                                    <div class="bt rm-bt">Supprimer la marque</div>
                                </fieldset>
                            <?php
                            endforeach; 
                            ?>
                            <div id="add-brand" class="bt add-bt">Ajouter une marque</div>
                        </fieldset>
                        <div class="bt rm-bt">Supprimer la société</div>
                    </fieldset>
                <?php
                endforeach; ?>
                <div id="add-cmp" class="bt add-bt">Ajouter une société</div>
            </div>
            <input type="submit" class="bt">
            <?php
        }
    }