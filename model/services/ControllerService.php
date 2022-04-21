<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Controller.php");

    class ControllerService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
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

            // if this lab exists, reutrn its id, else return -1
            return $row ? $row[0] : -1;
        }
    }            