<?php
    include_once(__DIR__ . "/AbstractEntity.php");

    class Contact extends AbstractEntity  {
        function __construct(private $firstname, private $lastname, private $role, private $email, private $phone = null) {}

        public function getFirstname()
        {
            return $this->firstname;
        }

        public function setFirstname($firstname)
        {
            $this->firstname = $firstname;

            return $this;
        }

        public function getLastname()
        {
            return $this->lastname;
        }

        public function setLastname($lastname)
        {
            $this->lastname = $lastname;

            return $this;
        }

        public function getRole()
        {
            return $this->role;
        }

        public function setRole($role)
        {
            $this->role = $role;

            return $this;
        }

        public function getEmail() : string
        {
            return $this->email;
        }

        public function setEmail($email)
        {
            $this->email = $email;

            return $this;
        }
 
        public function getPhone()
        {
            return $this->phone;
        }

        public function setPhone($phone)
        {
            // check validity
            $phoneCodes = ["+32", "+33", "+41"]; // Belgium, France, Switzerland

            $valid = false;
            foreach ($phoneCodes as $code) {
                if(strpos($phone, $code)) {
                    $valid = true;
                    break;
                }
            }
            if(!$valid)
                throw new Exception("L'index téléphonique fourni n'est pas supporté : ");

            $this->phone = $phone;

            return $this;
        }
    }