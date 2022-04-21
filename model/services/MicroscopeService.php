<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/LabService.php");
    include_once(__DIR__ . "/ContactService.php");
    include_once(__DIR__ . "/ModelService.php");
    include_once(__DIR__ . "/ControllerService.php");

    class MicroscopeService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
                self::$instance = new MicroscopeService();
           
            return self::$instance;
        }

        // TODO: handle keywords
        function addMicro(Microscope $micro) : int {
            global $pdo;
            
            $sth = $pdo->prepare("INSERT INTO microscope VALUES (NULL, :rate, :desc, :modId, :ctrId)");

            $sth->execute([
                "rate" => $micro->getRate(),
                "desc" => $micro->getDesc(),
                "modId" => ModelService::getInstance()->getModelId($micro->getModel()),
                "ctrId" => ControllerService::getInstance()->getControllerId($micro->getController())
            ]);     

            return $pdo->lastInsertId();
        }

        // TODO: check if the lab / brand / controller / are already in db, else add them but also add them in a table "to_verify", maybe in the add/save functions of each Service
        function addGroup(MicroscopesGroup $group) {
            global $pdo;
            
            //save the Lab
            $labId = LabService::getInstance()->save($group->getLab());

            // save the group and bind it to the lab
            $sth = $pdo->prepare("INSERT INTO microscopes_group VALUES (NULL, :lat, :lon, :labId)");

            $sth->execute([
                "lat" => $group->getLat(),
                "lon" => $group->getLon(),
                "labId" => $labId
            ]);

            // get the generated group id
            $groupId = $pdo->lastInsertId();
              
            // save the contact
            $contactId = ContactService::getInstance()->save($group->getContact());   
                
            // bind the contact to the group
            $sth = $pdo->prepare("INSERT INTO manage VALUES (:groupId, :contactId)");

            $sth->execute([
                "groupId" => $groupId,
                "contactId" => $contactId
            ]);

            // add the microscopes to the db
            foreach($group->getMicroscopes() as $micro)
                $this->addMicro($micro);
        }
    }