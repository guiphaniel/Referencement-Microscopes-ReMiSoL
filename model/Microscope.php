<?php
include_once("Coordinates.php");

// needs to implement JsonSerializable because fields are private
class Microscope implements JsonSerializable {
    private $labName;
    private $ref;
    private $desc;
    private $coor;

    public function jsonSerialize() : mixed {
        $reflector = new ReflectionClass('Microscope');

        $properties = $reflector->getProperties();
        
        $json = [];
        foreach($properties as $property) {
            $json[$property->getName()] = $property->getValue($this);
        }
        
        return $json;
    }

    public function getLabName()
    {
        return $this->labName;
    }

    public function setLabName($labName)
    {
        $this->labName = $labName;

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

    public function getDesc()
    {
        return $this->desc;
    }

    public function setDesc($desc)
    {
        $this->desc = $desc;

        return $this;
    }

    public function getCoor()
    {
        return $this->coor;
    }

    public function setCoor($coor)
    {
        $this->coor = $coor;

        return $this;
    }
}