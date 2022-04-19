<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Lab.php");

    class LabService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
                self::$instance = new LabService();
           
            return self::$instance;
        }

        function getLabId(Lab $lab) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM lab where name = :name");

            $sth->execute([
                "name" => $lab->getName()
            ]);

            $row = $sth->fetch();

            // if this lab exists, reutrn its id, else return -1
            return $row ? $row[0] : -1;
        }
    }            