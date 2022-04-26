<?php
include_once(__DIR__ . "/Coordinates.php");
include_once(__DIR__ . "/Model.php");
include_once(__DIR__ . "/Controller.php");
include_once(__DIR__ . "/Microscope.php");

class Microscope extends AbstractEntity  {

    function __construct(private Model $model, private Controller $controller, private string $rate, private string $desc, private array $keywords = []) {}
    
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

    public function getRate() : string
    {
        return $this->rate;
    }

    public function setRate(string $rate)
    {       
        $this->rate = $rate;

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

    public function getKeywords()
    {
        return $this->keywords;
    }

    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function addKeyword(Keyword $kw) 
    {
        $this->keywords[] = $kw;
    }

    public function removeKeyword(Keyword $kw) 
    {
        unset($this->keywords[$kw]);
    }
}