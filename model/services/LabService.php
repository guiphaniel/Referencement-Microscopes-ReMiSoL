<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Lab.php");
    
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class LabService extends AbstractService {
        static private $instance;

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
                select lab.id as labId, a.id as addrId, name, type, code, website, school, street, zip_code, city, country
                from lab
                join address as a
                on address_id = a.id
                where lab.id = $id
            ";

            $sth = $pdo->query($sql);
            $labInfos = $sth->fetch(PDO::FETCH_NAMED);

            $address = (new Address($labInfos["school"], $labInfos["street"], $labInfos["zip_code"], $labInfos["city"], $labInfos["country"]))
                ->setId($labInfos["addrId"]);
            return (new Lab($labInfos["name"], $labInfos["type"], $labInfos["code"], $labInfos["website"], $address))
                ->setId($labInfos["labId"]);
        }

        function findLabByGroupId($id) : Lab {
            global $pdo;

            $sql = "
                select lab.id as labId, a.id as addrId, name, type, code, website, school, street, zip_code, city, country
                from microscopes_group as g
                join lab
                on lab.id = g.lab_id
                join address as a
                on address_id = a.id
                where g.id = $id
            ";

            $sth = $pdo->query($sql);
            $labInfos = $sth->fetch(PDO::FETCH_NAMED);

            $address = (new Address($labInfos["school"], $labInfos["street"], $labInfos["zip_code"], $labInfos["city"], $labInfos["country"]))
                ->setId($labInfos["addrId"]);
            return (new Lab($labInfos["name"], $labInfos["type"], $labInfos["code"], $labInfos["website"], $address))
                ->setId($labInfos["labId"]);
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
                $lab->setId($id);
            }          

            return $id;
        }
    }            