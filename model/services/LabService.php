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

            // lab_name is unique
            $sth = $pdo->prepare("SELECT id FROM lab where lab_name = :name");

            $sth->execute([
                "name" => $lab->getName()
            ]);

            $row = $sth->fetch();

            // if this lab exists, reutrn its id, else return -1
            return $row ? $row[0] : -1;
        }

        /** Saves the lab if it doesn't exist yet, and returns its id */
        function save(Lab $lab) : int {
            global $pdo;
            
            $id = $this->getLabId($lab);
            
            // if the lab isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO lab VALUES (NULL, :type, :name, :address, :website)");

                $sth->execute([
                    "type" => $lab->getType(),
                    "name" => $lab->getName(),
                    "address" => $lab->getAddress(),
                    "website" => $lab->getWebsite()
                ]);

                $id = $pdo->lastInsertId();
            }          

            return $id;
        }
    }            