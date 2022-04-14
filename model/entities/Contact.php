<?php
    class Contact {
        function __construct(private $name, private $email) {}

        public function getName() : string
        {
            return $this->name;
        }

        public function setName($name)
        {
            $this->name = $name;

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