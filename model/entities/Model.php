<?php
    include_once("Brand.php");

    class Model implements JsonSerializable {
        function __construct(private string $name, private Brand $brand) {}
 
        public function jsonSerialize() : mixed {
            return [
                'name' => $this->name,
                'brand' => $this->brand
            ];
        }
        
        public function getName() : string
        {
            return $this->name;
        }

        public function setName(string $name)
        {
            $this->name = $name;

            return $this;
        }

        public function getBrand() : Brand
        {
            return $this->brand;
        }

        public function setBrand(Brand $brand)
        {
            $this->brand = $brand;

            return $this;
        }
    }