<?php
    include_once(__DIR__ . "/Person.php");

    class Contact extends Person  {
        function __construct(string $firstname, string $lastname, string $email, string $phoneCode, string $phoneNum, private string $role) {
            parent::__construct($firstname, $lastname, $email, $phoneCode, $phoneNum);
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
    }