<?php
    include_once("../include/config.php");
    include_once("../model/start_db.php");
    include_once("../model/services/KeywordService.php");
    include_once("../model/services/ModelService.php");
    include_once("../model/services/ControllerService.php");

    if(!isUserSessionValid() || !$_SESSION["user"]["admin"])
        redirect("/index.php");

    // check all fields at least have one input + no input is duplicated
    if(sizeof($_POST["cmps"]??[]) < 1) {
        $_SESSION["form"]["errorMsg"]="Vous devez saisir au moins une société.";
        redirect("/admin.php?action=micros");
    }

    $duplicates = getDuplicates($_POST["cmps"]);
    if($duplicates) {
        $_SESSION["form"]["errorMsg"]="Vous ne pouvez pas mettre deux fois la même société : " . $duplicates[0];
        redirect("/admin.php?action=micros");
    }

    foreach($_POST["cmps"] as $cmpId => $cmpName) {
        if(empty($_POST["brands"][$cmpId])) {
            $_SESSION["form"]["errorMsg"]="Vous devez saisir au moins une marque pour la société " . $cmpName;
            redirect("/admin.php?action=micros");
        }

        $duplicates = getDuplicates($_POST["brands"][$cmpId]);
        if($duplicates) {
            $_SESSION["form"]["errorMsg"]="Vous ne pouvez pas mettre deux fois la même marque : " . $duplicates[0] . " pour la société " . $cmpName;
            redirect("/admin.php?action=micros");
        }
        
        foreach($_POST["brands"][$cmpId] as $brandId => $brandName) {
            //models
            //empty?
            if(empty($_POST["models"][$cmpId][$brandId])) {
                $_SESSION["form"]["errorMsg"]="Vous devez saisir au moins un modèle pour la marque " . $brandName . " de la société " . $cmpName;
                redirect("/admin.php?action=micros");
            }
            //duplicates?
            $duplicates = getDuplicates($_POST["models"][$cmpId][$brandId]);
            if($duplicates) {
                $_SESSION["form"]["errorMsg"]="Vous ne pouvez pas mettre deux fois le même modèle : " . $duplicates[0] . " pour la marque " . $brandName . " de la société " . $cmpName;
                redirect("/admin.php?action=micros");
            }

            //controllers
            //empty?
            if(empty($_POST["ctrs"][$cmpId][$brandId])) {
                $_SESSION["form"]["errorMsg"]="Vous devez saisir au moins une électronique / contrôleur pour la marque " . $brandName . " de la société " . $cmpName;
                redirect("/admin.php?action=micros");
            }
            //duplicates?
            $duplicates = getDuplicates($_POST["models"][$cmpId][$brandId]);
            if($duplicates) {
                $_SESSION["form"]["errorMsg"]="Vous ne pouvez pas mettre deux fois la même électronique / contrôleur : " . $duplicates[0] . " pour la marque " . $brandName . " de la société " . $cmpName;
                redirect("/admin.php?action=micros");
            }
        }
    }

    function haveSameId($a, $b) { return $a->getId() - $b->getId(); }

    function update($service, $news, $olds) {
        $toInsert = array_udiff($news, $olds, "haveSameId");
        $toUpdate = array_uintersect($news, $olds, "haveSameId");
        $toDelete = array_udiff($olds, $news, "haveSameId");
        
        foreach ($toInsert as $e) 
            $service->save($e);
        
        foreach ($toUpdate as $e) 
            $service->update($olds[$e->getId()], $e);
        
        foreach ($toDelete as $e) 
            $service->delete($e);
    }

    function getDuplicates($array) {
        return array_filter(array_count_values($array), function ($nb) { return $nb > 1; });
    }

    // retrieve objects from inputs
    $cmps = [];
    $brands = [];
    $models = [];
    $ctrs = [];
    foreach($_POST["cmps"] as $cmpId => $cmpName) {
        $cmp = (new Compagny($cmpName))->setId($cmpId);
        $cmps[$cmpId] = $cmp;
        
        foreach($_POST["brands"][$cmpId] as $brandId => $brandName) {
            $brand = (new Brand($brandName, $cmp))->setId($brandId);
            $brands[$cmpId][$brandId] = $brand;

            foreach ($_POST["models"][$cmpId][$brandId] as $modelId => $modelName) {
                $model = (new Model($modelName, $brand))->setId($modelId);
                $models[$cmpId][$brandId][$modelId] = $model;
            }

            foreach ($_POST["ctrs"][$cmpId][$brandId] as $ctrId => $ctrName) {
                $ctr = (new Controller($ctrName, $brand))->setId($ctrId);
                $ctrs[$cmpId][$brandId][$ctrId] = $ctr;
            }
        }
    }

    // update objects in db
    $compagnyService = CompagnyService::getInstance();
    $brandService = BrandService::getInstance();
    $modelService = ModelService::getInstance();
    $ctrService = ControllerService::getInstance();

    update($compagnyService, $cmps, $compagnyService->getAllCompagnies());

    foreach($cmps as $cmpId => $cmp) {
        update($brandService, $brands[$cmpId], $brandService->getAllBrands($cmp));

        foreach($brands[$cmpId] as $brandId => $brand)
            update($modelService, $models[$cmpId][$brandId], $modelService->getAllModels($brand));
    
        foreach($brands[$cmpId] as $brandId => $brand)
            update($ctrService, $ctrs[$cmpId][$brandId], $ctrService->findAllControllersByBrand($brand));
    }

    

    redirect("/admin.php?action=micros");