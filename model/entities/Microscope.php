<?php
include_once("Coordinates.php");

// needs to implement JsonSerializable because fields are private
class Microscope implements JsonSerializable {

    function __construct(private $brand, private $ref) {}

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
}