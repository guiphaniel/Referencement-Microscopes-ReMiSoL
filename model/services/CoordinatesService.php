<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Coordinates.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });
    
    class CoordinatesService extends AbstractService {
        static private $instance;

        static function getInstance() : CoordinatesService {
            if(!isset(self::$instance))
                self::$instance = new CoordinatesService();
           
            return self::$instance;
        }

        function getCoordinatesId(Coordinates $coor) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM coordinates where lat = :lat and lon = :lon");

            $sth->execute([
                "lat" => $coor->getLat(), 
                "lon" => $coor->getLon()
            ]);

            $row = $sth->fetch();

            // if this coordinates exist, return their id, else return -1
            return $row ? $row[0] : -1;
        }

        function findCoordinatesById($id) : Coordinates {
            global $pdo;

            $sql = "
                select lat, lon
                from coordinates as c
                where c.id = $id
            ";

            $sth = $pdo->query($sql);
            $coorInfos = $sth->fetch(PDO::FETCH_NAMED);

            return (new Coordinates($coorInfos["lat"], $coorInfos["lon"]))->setId($id);
        }

        /** Saves the coordinates if they don't exist yet, else throws */
        function save(Coordinates $coor) : int {
            if($this->getCoordinatesId($coor) != -1)
                throw new Exception("Un groupe de microscopes existe déjà à cet emplacement");

            global $pdo;

            $sth = $pdo->prepare("INSERT INTO coordinates VALUES (NULL, :lat, :lon)");

            $sth->execute([
                "lat" => $coor->getLat(), 
                "lon" => $coor->getLon(),
            ]);

            $id = $pdo->lastInsertId();
            $coor->setId($id);  

            return $id;
        }
    }            