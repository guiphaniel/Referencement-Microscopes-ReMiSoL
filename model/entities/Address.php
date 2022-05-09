<?php
    include_once(__DIR__ . "/AbstractEntity.php");

    class Address extends AbstractEntity  {
        function __construct(private string $school, private string $street, private string $zipCode, private string $city, private string $country) {}

        public function toString() {
            return implode("\n", [$this->school, $this->address, $this->zipCode, $this->city, $this->country]);
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