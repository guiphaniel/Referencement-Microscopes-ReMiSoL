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

            $sth = $pdo->prepare("SELECT id FROM compagny where com_name = :name");

            $sth->execute([
                "name" => $compagny->getName()
            ]);

            $row = $sth->fetch();

            // if this compagny exists, return its id, else return -1
            return $row ? $row[0] : -1;
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
            }          

            return $id;
        }  

        function getAllCompagnies() : array {
            global $pdo;
            $compagnies = [];
            
            $sth = $pdo->query("SELECT com_name FROM compagny");

            $names = $sth->fetchAll(PDO::FETCH_COLUMN);

            foreach ($names as $name) {
                $compagnies[] = new Compagny($name);
            }

            return $compagnies;
        }
    }            