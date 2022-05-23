<?php
        include_once("AbstractEntity.php");
        include_once("Category.php");

    class Keyword extends AbstractEntity {
        function __construct(private Category $cat, private string $tag) {
            parent::__construct();
        }
 
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