<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Controller.php");
    
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class ControllerService extends AbstractService {
        static private $instance;

        static function getInstance() : ControllerService{
            if(!isset(self::$instance))
                self::$instance = new ControllerService();
           
            return self::$instance;
        }

         /** Saves the controller if it doesn't exist yet, and returns its id */
         function save(Controller $ctr) {
            global $pdo;

            $id = $this->getControllerId($ctr);

            // if the controller isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO controller VALUES (NULL, :name, :brandId)");
        
                $sth->execute([
                    "name" => $ctr->getName(),
                    "brandId" => BrandService::getInstance()->getBrandId($ctr->getBrand())
                ]);
                
                $id = $pdo->lastInsertId();
                $ctr->setId($id);
            }          

            return $id;
        }   

        //override : only admin can update controllers
        public function update(AbstractEntity $old, AbstractEntity $new) {
            if($_SESSION["user"]["admin"])
                parent::update($old, $new);
        }

        function getControllerId(Controller $controller) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM controller where name = :name");

            $sth->execute([
                "name" => $controller->getName()
            ]);

            $row = $sth->fetch();

            // if this lab exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findAllControllers($brand = null) : array {
            global $pdo;
            $controllers = [];
            if($brand?->getName() === "Homemade") // if the brand is Homemade, we want to retrieve all the controllers
                unset($brand);

            $sql = "SELECT * FROM controller";
            if (isset($brand)) {
                $brandId = $brand->getId();
                $sql .= " where brand_id = $brandId";
            }
            $sql .= " ORDER BY name";

            $sth = $pdo->query($sql);

            $infos = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($infos as $info) {
                $id = $info["id"];
                $controllers[$id] = (new Controller($info["name"], $brand??BrandService::getInstance()->findBrandById($info["brand_id"])))
                    ->setId($id);
            }

            return $controllers;
        }

        function findControllerByName($name) {
            global $pdo;

            $sql = "
                select *
                from controller
                where name = :name
            ";

            $sth = $pdo->prepare($sql);

            $sth->execute([
                "name" => $name
            ]);

            $infos = $sth->fetch(PDO::FETCH_NAMED);

            if(empty($infos))
                return null;

            return (new Controller($name, BrandService::getInstance()->findBrandById($infos["brand_id"])))
                ->setId($infos["id"]);
        }

        function bind($controllerId, $microId) {
            global $pdo;

            $pdo->exec("UPDATE microscope SET controller_id = $controllerId where id = $microId");
        }

        function unbind($controllerId, $microId) {
            global $pdo;

            $pdo->exec("UPDATE microscope SET controller_id = NULL where id = $microId");
        }
    }            