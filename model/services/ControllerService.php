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

        function getControllerId(Controller $controller) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM controller where ctr_name = :name");

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
                SELECT ctr_name, bra_name, com_name FROM controller
                JOIN brand as b
                on brand_id = b.id
                JOIN compagny as c
                on b.compagny_id = c.id
            ");

            foreach ($sth->fetchAll() as $row) {
                $controllers[] = new Controller($row["ctr_name"], new Brand($row["bra_name"], new Compagny($row["com_name"])));
            }

            return $controllers;
        }

        function findAllControllersByBrand(Brand $brand) : array {
            global $pdo;
            $controllers = [];
            
            $sth = $pdo->prepare("SELECT ctr_name FROM controller where brand_id = :brandId");

            $sth->execute([
                "brandId" => BrandService::getInstance()->getBrandId($brand)
            ]);

            $names = $sth->fetchAll(PDO::FETCH_COLUMN);

            foreach ($names as $name) {
                $controllers[] = new Controller($name, $brand);
            }

            return $controllers;
        }
    }            