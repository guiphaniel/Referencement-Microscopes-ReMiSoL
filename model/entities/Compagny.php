<?php
    class Compagny implements JsonSerializable {
        function __construct(private string $name) {}
 
        public function jsonSerialize() : mixed {
            return [
                'name' => $this->name
            ];
        }

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