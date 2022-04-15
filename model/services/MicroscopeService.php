<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/ContactService.php");
    include_once(__DIR__ . "/LabService.php");

    class MicroscopeService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
                self::$instance = new MicroscopeService();
           
            return self::$instance;
        }

        function getMicroscopeId(Microscope $micro) {
            global $pdo;

            $sth = $pdo->prepare("SELECT id FROM microscope where brand = :brand and ref = :ref");

            $sth->execute([
                "brand" => $micro->getBrand(),
                "ref" => $micro->getRef()
            ]);

            $row = $sth->fetch();

            // if this microscope exists, reutrn its id, else reutrn -1
            return $row ? $row[0] : -1;
        }

        function saveGroup(MicroscopesGroup $group) {
            global $pdo;
            
            // save the group and bind the lab
            $sth = $pdo->prepare("INSERT INTO microscopes_group VALUES (NULL, :lat, :lon, :labId)");

            $sth->execute([
                "lat" => $group->getLat(),
                "lon" => $group->getLon(),
                "labId" => LabService::getInstance()->getLabId($group->getLab())
            ]);

            // get the generated group id
            $groupId = $pdo->lastInsertId();
              
            // save the contact, if it's not yet in the db
            $contactService = ContactService::getInstance();
            $contactId = $contactService->getContactId($group->getContact());
            if ($contactId <= -1) {
                $contactService->save($group->getContact());   
                $contactId = $pdo->lastInsertId();
            }
                
            // bind the contact to the group
            $sth = $pdo->prepare("INSERT INTO manage VALUES (:contactId, :groupId)");

            $sth->execute([
                "contactId" => $contactId,
                "groupId" => $groupId
            ]);

            // bind microscopes to the group
            foreach($group->getMicroscopes() as $micro) {
                $microId = $this->getMicroscopeId($micro);
                $sth = $pdo->prepare("INSERT INTO belong VALUES (:groupId, :microId, :rate, :desc)");

                $sth->execute([
                    "groupId" => $groupId,
                    "microId" => $microId,
                    "rate" => $micro->getRate(),
                    "desc" => $micro->getDesc()
                ]);
            }
        }
    }