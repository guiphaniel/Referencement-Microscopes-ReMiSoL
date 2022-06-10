<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    include_once(__DIR__ . "/Address.php");
    
    class Lab extends AbstractEntity {
        private $code;

        function __construct(private string $name, private string $type, $code, private string $website, private Address $address) {
            parent::__construct();
            $this->setCode($code);
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

        public function getType()
        {
            return $this->type;
        }

        public function setType($type)
        {
            if(!in_array($type, ["UPR", "UMR","UAR", "FR", "EMR", "Autre"]))
                throw new Exception("Le code du laboratoire / service saisi est invalide");

            $this->type = $type;

            return $this;
        }

        public function getCode()
        {
            return $this->code;
        }

        public function setCode($code)
        {
            if($code != null && ($code < 10 || $code > 9999))
                throw new Exception("Le numéro du code du laboratoire / service saisi est invalide");

            $this->code = $code;

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

        public function getAddress() : Address
        {
            return $this->address;
        }

        public function setAddress(Address $address)
        {
            $this->address = $address;

            return $this;
        }
    }