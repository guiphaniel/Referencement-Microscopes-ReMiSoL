<?php
include_once(__DIR__ . "/Coordinates.php");

// needs to implement JsonSerializable because fields are private
class Microscope implements JsonSerializable {

    function __construct(private $brand, private $ref, private float $rate, private $desc) {}

    public function jsonSerialize() : mixed {
        $reflector = new ReflectionClass('Microscope');

        $properties = $reflector->getProperties();
        
        $json = [];
        foreach($properties as $property) 
            $json[$property->getName()] = $property->getValue($this);
        
        
        return $json;
    }

    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand($brand)
    {
        $this->brand = $brand;

        return $this;
    }

    public function getRef()
    {
        return $this->ref;
    }

    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }

    public function getRate() : float
    {
        return $this->rate;
    }

    public function setRate(float $rate)
    {
        if ($rate < 0.0)
            $rate = 0.0;
        
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
}