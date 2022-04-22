<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    
    class Lab extends AbstractEntity {
        function __construct(private string $name, private string $address) {}
 
        public function getName()
        {
            return $this->name;
        }

        public function setName(string $name)
        {
            $this->name = $name;

            return $this;
        }

        public function getAddress()
        {
            return $this->address;
        }

        public function setAddress(string $address)
        {
            $this->address = $address;

            return $this;
        }
    }