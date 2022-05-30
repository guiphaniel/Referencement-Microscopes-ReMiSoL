<?php
    include_once("../include/config.php");
    include_once("../model/start_db.php");
    include_once("../model/services/KeywordService.php");

    if(!isUserSessionValid() || !$_SESSION["user"]["admin"])
        redirect("/index.php");

    if(sizeof($_POST["cats"]??[]) < 1) {
        $_SESSION["form"]["errorMsg"]="Vous devez saisir au moins une catégorie.";
        redirect("/admin.php?action=keywords");
    }

    foreach(array_keys($_POST["cats"]??[]) as $catId) {
        if(empty($_POST["keywords"][$catId])) {
            $_SESSION["form"]["errorMsg"]="Vous devez saisir au moins un mot-clé pour la catégorie " . $_POST["cats"][$catId];
            redirect("/admin.php?action=keywords");
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

    // check there's no duplicated category
    foreach(array_count_values($_POST["cats"]) as $catName => $nb) {
        if($nb > 1) {
            $_SESSION["form"]["errorMsg"]="Vous ne pouvez pas mettre deux fois la même catégorie : $catName";
            redirect("/admin.php?action=keywords");
        }
    }

    // process categories
    $categoryService = CategoryService::getInstance();
   
    $newCats = array_map(function ($key, $cat) { return (new Category($cat))->setId($key); }, array_keys($_POST["cats"]), $_POST["cats"]);
    $oldCats = $categoryService->findAllCategories();

    update($categoryService, $newCats, $oldCats);

    // process keywords
    $keywordService = KeywordService::getInstance();

    $_POST["keywords"] = array_combine($_POST["cats"], $_POST["keywords"]); // update the categories name (bind id->name)

    // check there's no duplicated tag
    foreach ($_POST["keywords"] as $cat => $tags) {
        foreach(array_count_values($tags) as $tag => $nb) {
            if($nb > 1) {
                $_SESSION["form"]["errorMsg"]="Vous ne pouvez mettre qu'une fois l'étiquette $tag dans la catégorie $cat";
                redirect("/admin.php?action=keywords");
            }
        }
    }

    $newKws = [];
    foreach($_POST["keywords"] as $cat => $tags) {
        foreach($tags as $id => $tag)
            $newKws[] = (new Keyword(new Category($cat), $tag))->setId($id);

        update($keywordService, $newKws, $keywordService->findAllKeywords(new Category($cat)));
        $newKws = [];
    }

    redirect("/admin.php?action=keywords");