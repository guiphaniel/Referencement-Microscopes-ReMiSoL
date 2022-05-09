<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Address.php");

    class AddressService {
        static private $instance;

        private function __construct() {}

        static function getInstance() : AddressService{
            if(!isset(self::$instance))
                self::$instance = new AddressService();
           
            return self::$instance;
        }

        function getAddressId(Address $address) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM address where school = :school and address = :address and zipCode = :zipCode and city = :city and country = :country");

            $sth->execute([
                "school" => $address->getSchool(), 
                "address" => $address->getAddress(),
                "zipCode" => $address->getZipCode(),
                "city" => $address->getCity(),
                "country" => $address->getCountry()
            ]);

            $row = $sth->fetch();

            // if this address exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findAddressById($id) : Address {
            global $pdo;

            $sql = "
                select school, address, zipCode, city, country
                from address
                where address.id = $id
            ";

            $sth = $pdo->query($sql);
            $addressInfos = $sth->fetch(PDO::FETCH_NAMED);

            return new Address($addressInfos["school"], $addressInfos["address"], $addressInfos["zipCode"], $addressInfos["city"], $addressInfos["country"]);
        }

        /** Saves the address if it doesn't exist yet, and returns its id */
        function save(Address $address) : int {
            global $pdo;
            
            $id = $this->getAddressId($address);
            
            // if the address isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO address VALUES (NULL, :school, :address, :zipCode, :city, :country)");

                $sth->execute([
                    "school" => $address->getSchool(), 
                    "address" => $address->getAddress(),
                    "zipCode" => $address->getZipCode(),
                    "city" => $address->getCity(),
                    "country" => $address->getCountry()
                ]);

                $id = $pdo->lastInsertId();
            }          

            return $id;
        }
    }            