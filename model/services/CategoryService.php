<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Category.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class CategoryService extends AbstractService {
        static private $instance;

        static function getInstance() : CategoryService{
            if(!isset(self::$instance))
                self::$instance = new CategoryService();
           
            return self::$instance;
        }

        function getCategoryId(Category $category) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM category where name = :name");

            $sth->execute([
                "name" => $category->getName()
            ]);

            $row = $sth->fetch();

            // if this category exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        /** Saves the category if it doesn't exist yet, and returns its id */
        function save($category) {
            global $pdo;

            $id = $this->getCategoryId($category);

            // if the category isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO category VALUES (NULL, :name, :normName)");
        
                $sth->execute([
                    "name" => $category->getName(),
                    "normName" => $category->getNormName()
                ]);
                
                $id = $pdo->lastInsertId();
                $category->setId($id);
            }          

            return $id;
        }  

        //override : only admin can update categories
        public function update(AbstractEntity $old, AbstractEntity $new) {
            if($_SESSION["user"]["admin"])
                parent::update($old, $new);
        }

        function getAllCategories() : array {
            global $pdo;
            $categories = [];
            
            $sth = $pdo->query("SELECT id, name FROM category");

            $infos = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($infos as $info) {
                $categories[$info["id"]] = (new Category($info["name"]))->setId($info["id"]);
            }

            return $categories;
        }
    }            