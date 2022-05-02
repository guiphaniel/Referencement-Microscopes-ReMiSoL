<?php
    include_once(__DIR__ . "/AbstractEntity.php");

    class User extends AbstractEntity  {
        function __construct(private string $firstname, private string $lastname, private string $email, private string $phone, private string $password) {}

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

        public function getPhone() : string
        {
                return $this->phone;
        }

        public function setPhone(string $phone)
        {
                $this->phone = $phone;

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
    }