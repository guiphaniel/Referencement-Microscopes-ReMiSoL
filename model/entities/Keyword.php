<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    
    class Keyword extends AbstractEntity {
        function __construct(private string $cat, private string $tag) {}
 
        public function getCat()
        {
            return $this->cat;
        }

        public function setCat($cat)
        {
            $this->cat = $cat;

            return $this;
        }

        public function getTag()
        {
            return $this->tag;
        }

        public function setTag($tag)
        {
            $this->tag = $tag;

            return $this;
        }
    }