<?php
    class Compagny {
        function __construct(private string $name) {}
 
        public function getName() : string
        {
            return $this->name;
        }

        public function setName(string $name)
        {
            $this->name = $name;

            return $this;
        }
    }