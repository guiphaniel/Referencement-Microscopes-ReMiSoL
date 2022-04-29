<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/MicroscopeService.php");
    include_once(__DIR__ . "/LabService.php");
    include_once(__DIR__ . "/ContactService.php");

    class MicroscopesGroupService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
                self::$instance = new MicroscopesGroupService();
           
            return self::$instance;
        }

        // TODO: check if the lab / brand / controller / are already in db, else add them but also add them in a table "to_verify", maybe in the add/save functions of each Service
        function add(MicroscopesGroup $group) : int {
            global $pdo;

            // save the lab
            LabService::getInstance()->save($group->getLab());

            // save the group and bind it to the lab
            $sth = $pdo->prepare("INSERT INTO microscopes_group VALUES (NULL, :lat, :lon, :labId)");

            $sth->execute([
                "lat" => $group->getCoor()->getLat(),
                "lon" => $group->getCoor()->getLon(),
                "labId" => LabService::getInstance()->getLabId($group->getLab())
            ]);

            // get the generated group id
            $groupId = $pdo->lastInsertId(); 

            // save the contacts and bind them to the group...
            $contactService = ContactService::getInstance();
            foreach ($group->getContacts() as $contact) {
                $contactId = $contactService->save($contact); 
                $contactService->bind($contactId, $groupId); 
            }
                
            // add the microscopes to the db
            foreach($group->getMicroscopes() as $micro)
                MicroscopeService::getInstance()->add($groupId, $micro);

            $group->setId($groupId);

            return $groupId;
        }

        function getAllMicroscopesGroup() {
            global $pdo;

            // get groups infos
            $sql = "
                select id, lat, lon, lab_id
                from microscopes_group as g
            ";
            $sth = $pdo->query($sql);
            $groupsInfos = $sth->fetchAll(PDO::FETCH_NAMED);

            // generate groups
            $groups = [];
            foreach ($groupsInfos as $groupInfos) {
                $groupId = $groupInfos["id"];

                $lab = LabService::getInstance()->findLabById($groupInfos["lab_id"]);
                $contacts = $this->findAllContacts($groupId);
                $micros = $this->findAllMicroscopes($groupId);

                $group = new MicroscopesGroup(new Coordinates($groupInfos["lat"], $groupInfos["lon"]), $lab, $contacts);

                $group->setId($groupId);

                foreach ($micros as $micro) {
                    $group->addMicroscope($micro);
                }

                $groups[] = $group;
            }

            return $groups;
        }

        function findAllContacts($groupId) {
            global $pdo;

            $sql = "
                select firstname, lastname, role, email, phone
                from contact as c
                join manage as m
                on m.contact_id = c.id
                where microscopes_group_id = $groupId
            ";

            $sth = $pdo->query($sql);
            $contactsInfos = $sth->fetchAll(PDO::FETCH_NAMED);

            $contacts = [];
            foreach ($contactsInfos as $contactInfos) {
                $contacts[] = new Contact($contactInfos["firstname"], $contactInfos["lastname"], $contactInfos["role"], $contactInfos["email"], $contactInfos["phone"]);
            }

            return $contacts;
        } 
        
        function findAllMicroscopes($groupId) {
            global $pdo;

            $sql = "
                select id
                from microscope
                where microscopes_group_id = $groupId
            ";

            $sth = $pdo->query($sql);
            $microsIds = $sth->fetchAll(PDO::FETCH_COLUMN);

            $micros = [];
            $microscopeService = MicroscopeService::getInstance();
            foreach ($microsIds as $microId) {
                $micros[] = $microscopeService->findMicroscope($microId);
            }

            return $micros;
        }   

        function findMicroscopesGroupById(int $groupId) {
            global $pdo;

            // get groups infos
            $sql = "
                select g.id, lat, lon, lab_id
                from microscopes_group as g
                where g.id = $groupId
            ";
            $sth = $pdo->query($sql);
            $groupInfos = $sth->fetch(PDO::FETCH_NAMED);

            // if the group doesn't exist, return null
            if(!$groupInfos)
                return null;

            // generate the group
            $lab = LabService::getInstance()->findLabById($groupInfos["lab_id"]);
            $contacts = $this->findAllContacts($groupId);
            $micros = $this->findAllMicroscopes($groupId);

            $group = new MicroscopesGroup(new Coordinates($groupInfos["lat"], $groupInfos["lon"]), $lab, $contacts);

            $group->setId($groupId);

            foreach ($micros as $micro) {
                $group->addMicroscope($micro);
            }

            return $group;
        }
    }   