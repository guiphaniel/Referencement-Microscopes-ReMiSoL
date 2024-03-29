<?php
include_once(__DIR__ . "/Coordinates.php");
include_once(__DIR__ . "/Model.php");
include_once(__DIR__ . "/Controller.php");
include_once(__DIR__ . "/Microscope.php");
include_once(__DIR__ . "/../services/KeywordService.php");
include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

class Microscope extends AbstractEntity  {
    private string $rate;
    private string $descr;
    private string $normDescr;
    private string $type;
    private string $access;
    private array $keywords;

    function __construct(private Model $model, private Controller $controller, string $rate, string $descr, string $type, string $access, array $keywords) {
        parent::__construct();
        $this->setRate($rate);
        $this->setDescr($descr);
        $this->setType($type);
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

    public function getRate()
    {
        return $this->rate;
    }

    public function setRate($rate)
    {       
        if(!empty($rate) && !filter_var($rate, FILTER_VALIDATE_URL))
            throw new Exception("Veuillez saisir un site web valide pour votre tarification.");

        $this->rate = $rate;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        if($type != "LABO" && $type != "PLAT")
            throw new Exception('Ce type de microscope n\'est pas pris en charge. Valeurs possibles : "Laboratoire", "Plateforme".');

        $this->type = $type;

        return $this;
    }

    public function getDescr() : string
    {
        return $this->descr;
    }

    public function setDescr(string $descr)
    {
        $this->descr = $descr;
        $this->normDescr = strNormalize($descr);

        return $this;
    }

    public function getNormDescr() : string
    {
        return $this->normDescr;
    }

    public function getAccess()
    {
        return $this->access;
    }
 
    public function setAccess($access)
    {
        if($access != "ACAD" && $access != "INDU" && $access != "BOTH")
            throw new Exception("Ce type d'accès n'existe pas.");

        $this->access = $access;

        return $this;
    }

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        //remove duplicated tags
        $keywords = array_unique($keywords, SORT_REGULAR);

        // check if some of the cats provided aren't in the database
        $keywordService = KeywordService::getInstance();
        $extraCats = array_udiff(array_map(function($kw) {return $kw->getCat();}, $keywords), $keywordService->findAllCategories(), function ($a, $b) { return strcmp($a->getName(), $b->getName()); });
        if($extraCats)
            throw new Exception("Les catégories suivantes ne sont pas prises en charge : " . implode(", ", $extraCats) . ".");
        
        // check if some of the tags provided aren't in the database
        foreach ($keywords as $kw) {
            if(!in_array($kw->getTag() , $keywordService->findAllTags($kw->getCat())))
                throw new Exception('Le mot-clé "' . $kw->getTag() . '" n\'est pas pris en charge pour la catégorie "' . $kw->getCat() . '".');
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