<?php
    include_once(__DIR__ . "/Compagny.php");

    class Brand extends AbstractEntity {
        function __construct(private string $name, private Compagny $compagny) {
            parent::__construct();
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