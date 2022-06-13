<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    include_once(__DIR__ . "/Address.php");
    
    class Lab extends AbstractEntity {
        private string $type;
        private $code;
        private string $website;

        function __construct(private string $name, string $type, $code, string $website, private Address $address) {
            parent::__construct();
            $this->setType($type);
            $this->setCode($code);
            $this->setWebsite($website);
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
                throw new Exception("Le code du laboratoire / service saisi est invalide.");

            $this->type = $type;

            return $this;
        }

        public function getCode()
        {
            return $this->code;
        }

        public function setCode($code)
        {
            if(!empty($code) && (!is_numeric($code) || ($code < 10 || $code > 9999)))
                throw new Exception("Le numéro du code du laboratoire / service saisi est invalide.");

            $this->code = $code;

            return $this;
        }

        public function getWebsite()
        {
            return $this->website;
        }

        public function setWebsite($website)
        {
            if(!filter_var($website, FILTER_VALIDATE_URL))
                throw new Exception("Veuillez saisir un site web valide pour votre laboratoire.");

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