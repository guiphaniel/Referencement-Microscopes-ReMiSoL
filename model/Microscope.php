<?php
include_once("Coordinates.php");

class Microscope {
    private $labName;
    private $ref;
    private $desc;
    private $coor;

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