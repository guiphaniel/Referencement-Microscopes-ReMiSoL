<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

    class Category extends AbstractEntity  {
        private string $name;
        private string $normName;

        
        function __construct(string $name) {
            parent::__construct();

            $this->setName($name);
        }

        public function getName() : string
        {
            return $this->name;
        }

        public function setName(string $name)
        {
            $this->name = $name;
            $this->normName = strNormalize($name);

            return $this;
        }
    }