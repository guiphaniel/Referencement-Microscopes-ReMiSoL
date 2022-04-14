<?php
    include_once("../start_db.php");
    include_once("../entities/MicroscopeGroup.php");
    include_once("../services/ContactService.php");

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

            return $sth->fetch()[0];
        }

        function saveGroup(MicroscopesGroup $group) {
            global $pdo;
            
            // save the group and bind the lab
            $sth = $pdo->prepare("INSERT INTO microscopes_group VALUES (NULL, :lat, :lon, :labName)");
            $groupId = $pdo->lastInsertId();

            $sth->execute([
                "lat" => $group->getLat(),
                "lon" => $group->getLon(),
                "labName" => $group->getLab()->getName()
            ]);

              
            // save the contact, if it's not yet in the db
            $contactService = ContactService::getInstance();
            if (!$contactService->getContactId($group->getContact()) >= 0)
                $contactService->save($group->getContact());            

            // bind microscopes to the group
            foreach($group->getMicroscopes() as $micro) {
                $microId = getMicroscopeId($micro);
                $sth = $pdo->prepare("INSERT INTO belong VALUES (:groupId, microId, :rate, :desc)");

                $sth->execute([
                    "groupId" => $groupId,
                    "microId" => $microId,
                    "rate" => $micro->getRate(),
                    "desc" => $micro->getDesc()
                ]);
            }
        }
    }