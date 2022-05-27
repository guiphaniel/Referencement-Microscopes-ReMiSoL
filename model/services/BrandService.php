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
                where b.name = :brandName and c.name = :compName
            ");

            $sth->execute([
                "brandName" => $brand->getName(), 
                "compName" => $brand->getCompagny()->getName()
            ]);

            $row = $sth->fetch();

            // if this brand exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findBrandById($id) {
            global $pdo;

            $sql = "
                select *
                from brand
                where id = $id
            ";

            $sth = $pdo->query($sql);
            $infos = $sth->fetch(PDO::FETCH_NAMED);

            if(empty($infos))
                return null;

            return (new Brand($infos["name"], CompagnyService::getInstance()->findCompagnyById($infos["compagny_id"])))
                ->setId($id);
        }

        function findBrandByName($name) {
            global $pdo;

            $sql = "
                select *
                from brand
                where name = :name
            ";

            $sth = $pdo->prepare($sql);

            $sth->execute([
                "name" => $name
            ]);

            $infos = $sth->fetch(PDO::FETCH_NAMED);

            if(empty($infos))
                return null;

            return (new Brand($name, CompagnyService::getInstance()->findCompagnyById($infos["compagny_id"])))
                ->setId($infos["id"]);
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
                $brand->setId($id);
            }          

            return $id;
        }  

        //override : only admin can update brands
        public function update(AbstractEntity $old, AbstractEntity $new) {
            if($_SESSION["user"]["admin"])
                parent::update($old, $new);
        }

        function getAllBrands($compagny = null) : array {
            global $pdo;
            $brands = [];
            
            $sql = "SELECT * FROM brand";
            if (isset($compagny)) {
                $compagnyId = $compagny->getId();
                $sql .= " where compagny_id = $compagnyId";
            }
            $sql .= " ORDER BY name";

            $sth = $pdo->query($sql);

            $infos = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($infos as $info) {
                $id = $info["id"];
                $brands[$id] = (new Brand($info["name"], $compagny??CompagnyService::getInstance()->findCompagnyById($info["compagny_id"])))->setId($id);
            }

            return $brands;
        }
    }            