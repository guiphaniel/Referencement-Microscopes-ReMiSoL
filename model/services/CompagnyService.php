<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Compagny.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class CompagnyService extends AbstractService {
        static private $instance;

        static function getInstance() : CompagnyService{
            if(!isset(self::$instance))
                self::$instance = new CompagnyService();
           
            return self::$instance;
        }

        function getCompagnyId(Compagny $compagny) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM compagny where name = :name");

            $sth->execute([
                "name" => $compagny->getName()
            ]);

            $row = $sth->fetch();

            // if this compagny exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findCompagnyById($id) {
            global $pdo;

            $sql = "
                select name
                from compagny
                where id = $id
            ";

            $sth = $pdo->query($sql);
            $infos = $sth->fetch(PDO::FETCH_NAMED);

            if(empty($infos))
                return null;

            return (new Compagny($infos["name"]))
                ->setId($id);
        }

        function findCompagnyByName($name) {
            global $pdo;

            $sql = "
                select *
                from compagny
                where name = :name
            ";

            $sth = $pdo->prepare($sql);

            $sth->execute([
                "name" => $name
            ]);

            $infos = $sth->fetch(PDO::FETCH_NAMED);

            if(empty($infos))
                return null;

            return (new Compagny($name))
                ->setId($infos["id"]);
        }

        /** Saves the compagny if it doesn't exist yet, and returns its id */
        function save(Compagny $compagny) {
            global $pdo;

            $id = $this->getCompagnyId($compagny);

            // if the compagny isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO compagny VALUES (NULL, :name)");
        
                $sth->execute([
                    "name" => $compagny->getName()
                ]);
                
                $id = $pdo->lastInsertId();
                $compagny->setId($id);
            }          

            return $id;
        }  

        //override : only admin can update compagnies
        public function update(AbstractEntity $old, AbstractEntity $new) {
            if($_SESSION["user"]["admin"])
                parent::update($old, $new);
        }

        function findAllCompagnies() : array {
            global $pdo;
            $compagnies = [];
            
            $sth = $pdo->query("SELECT id, name FROM compagny ORDER BY name");

            $infos = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($infos as $info) {
                $id = $info["id"];
                $compagnies[$id] = (new Compagny($info["name"]))->setId($id);
            }

            return $compagnies;
        }
    }            