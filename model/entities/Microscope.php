<?php
include_once(__DIR__ . "/Coordinates.php");
include_once(__DIR__ . "/Model.php");
include_once(__DIR__ . "/Controller.php");
include_once(__DIR__ . "/Microscope.php");
include_once(__DIR__ . "/../services/Microscope.php");

class Microscope extends AbstractEntity  {

    function __construct(private Model $model, private Controller $controller, private string $desc, string $access, private $rate = null, array $keywords = []) {
        $this->setAccess($access);
        $this->setKeywords($keywords);
    }
    
    public function getModel() : Model
    {
        return $this->model;
    }
    
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }
    
    public function getController() : Controller
    {
        return $this->controller;
    }

    public function setController(Controller $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    public function getDesc() : string
    {
        return $this->desc;
    }

    public function setDesc(string $desc)
    {
        $this->desc = $desc;

        return $this;
    }

    public function getAccess()
    {
        return $this->access;
    }
 
    public function setAccess($access)
    {
        if($access != "ACAD" || $access != "INDU" || $access != "BOTH")
            throw new Exception("Ce type d'accès n'existe pas.");

        $this->access = $access;

        return $this;
    }

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {       
        $this->rate = $rate;

        return $this;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        // check if some of the cats provided aren't in the database
        $keywordService = KeywordService::getInstance();
        $extraCats = array_diff(array_keys($keywords), $keywordService->getAllCategories());
        if($extraCats)
            throw new Exception("Les catégories suivantes ne sont pas prises en charge : " . implode(", ", $extraCats));
        
        // check if some of the tags provided aren't in the database
        foreach ($keywords as $cat => $tags) {
            $extraTags = array_diff($tags, $keywordService->getAllTags($cat));
            if($extraTags)
                throw new Exception('Les mots-clés "' . implode('", "', $extraTags) . '" ne sont pas pris en charge pour la catégorie "' . $cat . '"');
        }

        $this->keywords = $keywords;

        return $this;
    }

    public function addKeyword($cat, $tag) 
    {
        $this->keywords[$cat][] = $tag;
    }

    public function removeKeyword($cat, $tag) 
    {
        unset($keywords[$cat][array_search($tag, $this->keywords, true)]);
    }
}