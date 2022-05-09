<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Lab.php");
    include_once(__DIR__ . "/../services/AddressService.php");

    class LabService {
        static private $instance;

        private function __construct() {}

        static function getInstance() : LabService{
            if(!isset(self::$instance))
                self::$instance = new LabService();
           
            return self::$instance;
        }

        function getLabId(Lab $lab) {
            global $pdo;

            // code is unique
            $sth = $pdo->prepare("SELECT id FROM lab where code = :code");

            $sth->execute([
                "code" => $lab->getCode()
            ]);

            $row = $sth->fetch();

            // if this lab exists, reutrn its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findLabById($id) : Lab {
            global $pdo;

            $sql = "
                select lab_name as name, type, code, website, school, street, zipCode, city, country
                from lab
                join address as a
                on address_id = a.id
                where lab.id = $id
            ";

            $sth = $pdo->query($sql);
            $labInfos = $sth->fetch(PDO::FETCH_NAMED);

            $address = new Address($labInfos["school"], $labInfos["street"], $labInfos["zipCode"], $labInfos["city"], $labInfos["country"]);
            return new Lab($labInfos["name"], $labInfos["type"], $labInfos["code"], $labInfos["website"], $address);
        }

        /** Saves the lab if it doesn't exist yet, and returns its id */
        function save(Lab $lab) : int {
            global $pdo;
            
            $id = $this->getLabId($lab);
            
            // if the lab isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO lab VALUES (NULL, :name, :type, :code, :website, :addressId)");

                $sth->execute([
                    "name" => $lab->getName(),
                    "type" => $lab->getType(),
                    "code" => $lab->getCode(),
                    "website" => $lab->getWebsite(),
                    "addressId" => AddressService::getInstance()->save($lab->getAddress())
                ]);
                $id = $pdo->lastInsertId();
            }          

            return $id;
        }
    }            