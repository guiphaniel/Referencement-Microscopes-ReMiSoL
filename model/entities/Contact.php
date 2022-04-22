<?php
    include_once(__DIR__ . "/AbstractEntity.php");

    class Contact extends AbstractEntity  {
        function __construct(private $firstname, private $lastname, private $email) {}

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

        public function getEmail() : string
        {
            return $this->email;
        }

        public function setEmail($email)
        {
            $this->email = $email;

            return $this;
        }
    }