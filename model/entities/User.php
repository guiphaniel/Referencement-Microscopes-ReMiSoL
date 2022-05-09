<?php
    include_once(__DIR__ . "/AbstractEntity.php");

    class User extends AbstractEntity  {
        function __construct(private string $firstname, private string $lastname, private string $email, private $phoneCode, private $phoneNum, private string $password, private bool $locked = true, private bool $admin = false) {}

        public function getFirstname() : string
        {
            return $this->firstname;
        }

        public function setFirstname(string $firstname)
        {
            $this->firstname = $firstname;

            return $this;
        }

        public function getLastname() : string
        {
                return $this->lastname;
        }

        public function setLastname(string $lastname)
        {
                $this->lastname = $lastname;

                return $this;
        }

        public function getEmail() : string
        {
                return $this->email;
        }

        public function setEmail(string $email)
        {
                $this->email = $email;

                return $this;
        }

        public function getPhoneCode()
        {
                return $this->phoneCode;
        }

        public function setPhoneCode($phoneCode)
        {
            $codes = ["+32", "+33", "+41"]; // Belgium, France, Switzerland

            $valid = false;
            foreach ($codes as $code) {
                if(strpos($phoneCode, $code)) {
                    $valid = true;
                    break;
                }
            }
            if(!$valid)
                throw new Exception("L'index téléphonique fourni n'est pas supporté");

            $this->phoneCode = $phoneCode;

            return $this;
        }

        public function getPhoneNum()
        {
                return $this->phoneNum;
        }

        public function setPhoneNum($phoneNum)
        {
                $this->phoneNum = $phoneNum;

                return $this;
        }

        public function getPassword() : string
        {
                return $this->password;
        }

        public function setPassword(string $password)
        {
                $this->password = $password;

                return $this;
        }

        public function isLocked()
        {
                return $this->locked;
        }

        public function setLocked($locked)
        {
                $this->locked = $locked;

                return $this;
        }

        public function isAdmin()
        {
                return $this->admin;
        }

        public function setAdmin($admin)
        {
                $this->admin = $admin;

                return $this;
        }
    }