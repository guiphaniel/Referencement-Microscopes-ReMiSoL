<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    
    class Lab extends AbstractEntity {
        function __construct(string $type, private string $name, private string $address, private string $website) {
            $this->setType($type);
        }

        public function getType()
        {
            return $this->type;
        }

        public function setType($type)
        {
            if($type != "LABO" || $type != "SERV")
                throw new Exception("Ce type de structure n'est pas pris en charge.");
    
            $this->type = $type;
    
            return $this;
        }

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

        public function getWebsite()
        {
                return $this->website;
        }

        public function setWebsite($website)
        {
                $this->website = $website;

                return $this;
        }
    }