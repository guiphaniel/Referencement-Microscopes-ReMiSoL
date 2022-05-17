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

        function findAllControllers() : array {
            global $pdo;
            $controllers = [];
            
            $sth = $pdo->query("
                SELECT c.id, c.name as ctrName, b.name as braName, c.name as comName FROM controller
                JOIN brand as b
                on brand_id = b.id
                JOIN compagny as c
                on b.compagny_id = c.id
            ");

            foreach ($sth->fetchAll() as $row) {
                $controllers[] = (new Controller($row["ctrName"], new Brand($row["braName"], new Compagny($row["comName"]))))
                    ->setId($row["id"]);
            }

            return $controllers;
        }

        function findAllControllersByBrand(Brand $brand) : array {
            global $pdo;
            $controllers = [];
            
            $sth = $pdo->prepare("SELECT id, name FROM controller where brand_id = :brandId");

            $sth->execute([
                "brandId" => BrandService::getInstance()->getBrandId($brand)
            ]);

            $infos = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($infos as $info) {
                $controllers[] = (new Controller($info["name"], $brand))
                    ->setId($info["id"]);
            }

            return $controllers;
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