<?php
    include_once(__DIR__ . "/AbstractEntity.php");

    class Address extends AbstractEntity  {
        private string $zipCode;

        function __construct(private string $school, private string $street, string $zipCode, private string $city, private string $country) {
            parent::__construct();

            $this->setZipCode($zipCode);
        }

        public function toString() {
            return implode("\n", array_filter([$this->school, $this->street, $this->zipCode, $this->city, $this->country]));
        }

        public function getSchool()
        {
            return $this->school;
        }

        public function setSchool($school)
        {
            $this->school = $school;

            return $this;
        }
 
        public function getStreet()
        {
            return $this->street;
        }

        public function setStreet($street)
        {
            $this->street = $street;

            return $this;
        }

        public function getZipCode()
        {
            return $this->zipCode;
        }

        public function setZipCode($zipCode)
        {
            if(!preg_match("/^\d{4,5}$/", $zipCode))
                throw new Exception("Veuillez saisir un code postal valide.");

            $this->zipCode = $zipCode;

            return $this;
        }

        public function getCity()
        {
            return $this->city;
        }
 
        public function setCity($city)
        {
            $this->city = $city;

            return $this;
        }

        public function getCountry()
        {
            return $this->country;
        }

        public function setCountry($country)
        {
            $this->country = $country;

            return $this;
        }
    }