<?php
    class Contact implements JsonSerializable {
        function __construct(private $firstname, private $lastname, private $email) {}

        public function jsonSerialize() : mixed {
            $reflector = new ReflectionClass('Contact');
            $properties = $reflector->getProperties();
            
            $json = [];
            foreach($properties as $property) {
                $json[$property->getName()] = $property->getValue($this);
            }
            
            return $json;
        }

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