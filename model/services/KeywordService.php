<?php
    include_once(__DIR__ . "/../start_db.php");

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

        function getKeywordId($cat, $tag) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM keyword where cat = :cat and tag = :tag");

            $sth->execute([
                "cat" => $cat,
                "tag" => $tag
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
    }            