<?php
    include_once(__DIR__ . "/Person.php");

    class User extends Person  {
        function __construct(string $firstname, string $lastname, string $email, string $phoneCode, string $phoneNum, private string $password, private bool $locked = true, private bool $admin = false) {
            parent::__construct($firstname, $lastname, $email, $phoneCode, $phoneNum);
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