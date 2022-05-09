<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    include_once(__DIR__ . "/Address.php");
    
    class Lab extends AbstractEntity {
        private string $code;

        function __construct(private string $name, string $code, private string $website, private Address $address) {
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

        public function getCode()
        {
            return $this->code;
        }

        public function setCode($code)
        {
            //check that the code has the good format
            if(!preg_match("/^(UPR|UMR|IRL|UAR|FR|EMR)\d{2,4}$/", $code))
                throw new Exception("Le code du laboratoire / service saisi est invalide");

            //check that the code number is between 10-9999 (because the previous regex would evaluate 001 to true)
            $matches = [];
            preg_match("/\d{2,4}/", $code, $matches, PREG_OFFSET_CAPTURE);

            $foundCode = intval(substr($code, $matches[0][1]));

            if($foundCode < 10 || $foundCode > 9999)
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