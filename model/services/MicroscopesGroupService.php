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

            return $groupId;
        }

        // TODO: fetch keywords too
        function getAllMicroscopesGroup() {
            global $pdo;

            // get groups infos
            $sql = "
                select g.id, lat, lon
                from microscopes_group as g
                join lab as l
                on l.id = g.lab_id
            ";
            $sth = $pdo->query($sql);
            $groupsInfos = $sth->fetchAll(PDO::FETCH_NAMED);

            // generate groups
            $groups = [];
            foreach ($groupsInfos as $groupInfo) {
                $groupId = $groupInfo["id"];

                $lab = $this->findLab($groupId);
                $contacts = $this->findAllContacts($groupId);
                $micros = $this->findAllMicroscopes($groupId);

                $group = new MicroscopesGroup(new Coordinates($groupInfo["lat"], $groupInfo["lon"]), $lab, $contacts);

                foreach ($micros as $micro) {
                    $group->addMicroscope($micro);
                }

                $groups[] = $group;
            }

            return $groups;
        }

        function findLab($groupId) {
            global $pdo;

            $sql = "
                select lab_name as name, address
                from microscopes_group as mg
                join lab as l
                on mg.lab_id = l.id
                where mg.id = $groupId
            ";

            $sth = $pdo->query($sql);
            $labInfos = $sth->fetch(PDO::FETCH_NAMED);

            return new Lab($labInfos["name"], $labInfos["address"]);
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
                select mi.id as microId, com_name as compagnyName, bra_name as brandName, mod_name as modelName, ctr_name as controllerName, rate, desc 
                from microscope as mi
                join controller as ctr
                on ctr.id = mi.controller_id
                join model as mod
                on mod.id = mi.model_id
                join brand as bra
                on bra.id = mod.brand_id
                join compagny as com
                on com.id = bra.compagny_id
                where microscopes_group_id = $groupId
            ";

            $sth = $pdo->query($sql);
            $microsInfos = $sth->fetchAll(PDO::FETCH_NAMED);

            $micros = [];
            foreach ($microsInfos as $microInfos) {
                $com = new Compagny($microInfos["compagnyName"]);
                $bra = new Brand($microInfos["brandName"], $com);
                $mod = new Model($microInfos["modelName"], $bra);
                $ctr = new Controller($microInfos["controllerName"], $bra);

                $kws = MicroscopeService::getInstance()->findAllKeywords($microInfos["microId"]);

                $micros[] = new Microscope($mod, $ctr, $microInfos["rate"], $microInfos["desc"], $kws);
            }

            return $micros;
        }        
    }   