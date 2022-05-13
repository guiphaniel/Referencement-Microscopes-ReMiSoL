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
            global $pdo;

            $id = $this->getKeywordId($kw);

            // if the keyword isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO keyword VALUES (NULL, :cat, :tag)");
        
                $sth->execute([
                    "cat" => $kw->getCat(),
                    "tag" => $kw->getTag()
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

            $sth = $pdo->prepare("SELECT id FROM keyword where cat = :cat and tag = :tag");

            $sth->execute([
                "cat" => $kw->getCat(),
                "tag" => $kw->getTag()
            ]);

            $row = $sth->fetch();

            // if this keyword exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function getAllCategories(){
            global $pdo;

            $sth = $pdo->query("SELECT DISTINCT cat FROM keyword");

            $row = $sth->fetchAll(PDO::FETCH_COLUMN);

            // if this keyword exists, return its id, else return -1
            return $row ? $row : [];
        }

        function getAllTags($cat) {
            global $pdo;

            $sth = $pdo->prepare("SELECT tag FROM keyword where cat = :cat");

            $sth->execute([
                "cat" => $cat
            ]);

            $row = $sth->fetchAll(PDO::FETCH_COLUMN);

            // if this keyword exists, return its id, else return -1
            return $row ? $row : [];
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