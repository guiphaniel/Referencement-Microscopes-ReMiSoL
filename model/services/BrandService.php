<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Brand.php");
    
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class BrandService extends AbstractService {
        static private $instance;

        static function getInstance() : BrandService {
            if(!isset(self::$instance))
                self::$instance = new BrandService();
           
            return self::$instance;
        }

        function getBrandId(Brand $brand) {
            global $pdo;

            $sth = $pdo->prepare("
                SELECT b.id
                FROM brand as b
                Join compagny as c
                on compagny_id = c.id
                where bra_name = :brandName and com_name = :compName
            ");

            $sth->execute([
                "brandName" => $brand->getName(), 
                "compName" => $brand->getCompagny()->getName()
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