<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Keyword.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class KeywordService extends AbstractService {
        static private $instance;

        static function getInstance() : KeywordService{
            if(!isset(self::$instance))
                self::$instance = new KeywordService();
           
            return self::$instance;
        }

        /** Saves the keyword if it doesn't exist yet, and returns its id */
        function save(Keyword $kw) {
            if(!$_SESSION["user"]["admin"])
                return -1;

            global $pdo;

            $id = $this->getKeywordId($kw);

            // if the keyword isn't already in the db, add it
            if ($id == -1)  {
                // if the category isn't already in the db, add it
                $categoryService = CategoryService::getInstance();
                $cat = $kw->getCat();
                $catId = $categoryService->getCategoryId($cat);
                if($catId == -1) $catId = $categoryService->save($cat);

                // insert the keyword
                $sth = $pdo->prepare("INSERT INTO keyword VALUES (NULL, :catId, :tag, :normTag)");
        
                $sth->execute([
                    "catId" => $catId,
                    "tag" => $kw->getTag(),
                    "normTag" => $kw->getNormTag()
                ]);
                
                $id = $pdo->lastInsertId();
                $kw->setId($id);
            }          

            return $id;
        }  

        //override : only admin can update keywords
        public function update(AbstractEntity $old, AbstractEntity $new) {
            if($_SESSION["user"]["admin"])
                parent::update($old, $new);
        }

        function getKeywordId(Keyword $kw) {
            global $pdo;

            $sth = $pdo->prepare("SELECT k.id FROM keyword as k JOIN category as c on k.category_id = c.id where c.name = :cat and tag = :tag");

            $sth->execute([
                "cat" => $kw->getCat()->getName(),
                "tag" => $kw->getTag()
            ]);

            $row = $sth->fetch();

            // if this keyword exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function getAllCategories() {
            return CategoryService::getInstance()->getAllCategories();
        }

        function getAllTags($cat) {
            global $pdo;

            $sth = $pdo->prepare("SELECT k.id, tag FROM keyword as k JOIN category as c on k.category_id = c.id where c.name = :cat");

            $sth->execute([
                "cat" => $cat->getName()
            ]);

            $rows = $sth->fetchAll(PDO::FETCH_NAMED);

            $tags = [];
            foreach($rows as $row)
                $tags[$row["id"]] = $row["tag"];

            return $tags;
        }

        function getAllKeywords($cat = null) {
            global $pdo;

            $sql = "SELECT k.id, c.name, tag FROM keyword as k JOIN category as c on k.category_id = c.id";
            if(isset($cat)) {
                $catName = $pdo->quote($cat->getName());
                $sql .= " WHERE c.name = $catName";
            }

            $sth = $pdo->query($sql);

            $rows = $sth->fetchAll(PDO::FETCH_NAMED);

            $kws = [];

            foreach($rows as $row)
                $kws[$row["id"]] = (new Keyword(new Category($row["name"]), $row["tag"]))->setId($row["id"]);

            return $kws;
        }

        function bind($kwId, $microId) {
            global $pdo;

            $sth = $pdo->prepare("INSERT INTO microscope_keyword VALUES (:microId, :kwId)");

            $sth->execute([
                "microId" => $microId,
                "kwId" => $kwId
            ]);
        }

        function unbind($kwId, $microId) {
            global $pdo;

            $sth = $pdo->prepare("DELETE FROM microscope_keyword WHERE microscope_id = :microId AND keyword_id = :kwId");

            $sth->execute([
                "microId" => $microId,
                "kwId" => $kwId
            ]);
        }
    }            