<?php
    include_once("Compagny.php");

    class Brand implements JsonSerializable {
        function __construct(private string $name, private Compagny $compagny) {}
 
        public function jsonSerialize() : mixed {
            return [
                'name' => $this->name,
                'compagny' => $this->compagny
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

        public function getCompagny() : Compagny
        {
            return $this->compagny;
        }

        public function setCompagny(Compagny $compagny)
        {
            $this->compagny = $compagny;

            return $this;
        }
    }