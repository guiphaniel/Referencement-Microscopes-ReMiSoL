<?php
        include_once("AbstractEntity.php");
        include_once("Category.php");
        include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");

    class Keyword extends AbstractEntity {
        private string $tag;
        private string $normTag;
            
        function __construct(private Category $cat, string $tag) {
            parent::__construct();

            $this->setTag($tag);
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
                $this->normTag = strNormalize($tag);

                return $this;
        }
    }