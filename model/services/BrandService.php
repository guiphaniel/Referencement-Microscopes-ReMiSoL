<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Brand.php");
    include_once(__DIR__ . "/CompagnyService.php");

    class BrandService {
        static private $instance;

        private function __construct() {}

        static function getInstance() : BrandService {
            if(!isset(self::$instance))
                self::$instance = new BrandService();
           
            return self::$instance;
        }

        function getBrandId(Brand $brand) {
            global $pdo;

            // there is no need to check for the compagny, as bra_name is unique
            $sth = $pdo->prepare("SELECT id FROM brand where bra_name = :name");

            $sth->execute([
                "name" => $brand->getName()
            ]);

            $row = $sth->fetch();

            // if this brand exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        /** Saves the brand if it doesn't exist yet, and returns its id */
        function save(Brand $brand) {
            global $pdo;

            $id = $this->getBrandId($brand);

            // if the brand isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO brand VALUES (NULL, :name, :compagnyId)");
        
                $sth->execute([
                    "name" => $brand->getName(),
                    "compagnyId" => CompagnyService::getInstance()->getCompagnyId($brand->getCompagny())
                ]);
                
                $id = $pdo->lastInsertId();
            }          

            return $id;
        }  

        function getAllBrands(Compagny $compagny) : array {
            global $pdo;
            $brands = [];
            
            $sth = $pdo->prepare("SELECT bra_name FROM brand where compagny_id = :compagnyId");

            $sth->execute([
                "compagnyId" => CompagnyService::getInstance()->getCompagnyId($compagny)
            ]);

            $names = $sth->fetchAll(PDO::FETCH_COLUMN);

            foreach ($names as $name) {
                $brands[] = new Brand($name, $compagny);
            }

            return $brands;
        }
    }            