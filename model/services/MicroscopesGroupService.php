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

            // save the contact...
            ContactService::getInstance()->save($group->getContact()); 

            // ... and bind it to the group
            $sth = $pdo->prepare("INSERT INTO manage VALUES (:groupId, :contactId)");

            $sth->execute([
                "groupId" => $groupId,
                "contactId" => ContactService::getInstance()->getContactId($group->getContact())
            ]);
                
            // add the microscopes to the db
            foreach($group->getMicroscopes() as $micro)
                MicroscopeService::getInstance()->add($groupId, $micro);

            return $groupId;
        }

        // TODO: fetch keywords too
        function getAllMicroscopesGroup() {
            global $pdo;
            $groupsResult = [];

            $sql = "
                select g.id, lat, lon, lab_name as labName, address as labAddress, firstname as contactFirstname, lastname as contactLastname, email as contactEmail from microscopes_group as g
                join lab as l
                on l.id = g.lab_id
                join contact as con
                on con.id = g.contact_id
            ";
            $sth = $pdo->query($sql);
            $groups = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($groups as $group) {
                $groupId = $group["id"];
                $sql = "
                    select com_name as compagnyName, bra_name as brandName, mod_name as modelName, ctr_name as controllerName, rate, desc 
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
                $microscopes = $sth->fetchAll(PDO::FETCH_NAMED);

                $groupResult = new MicroscopesGroup(new Coordinates($group["lat"], $group["lon"]), new Lab($group["labName"], $group["labAddress"]), new Contact($group["contactFirstname"], $group["contactLastname"], $group["contactEmail"]));
                foreach ($microscopes as $micro) {
                    $com = new Compagny($micro["compagnyName"]);
                    $bra = new Brand($micro["brandName"], $com);
                    $mod = new Model($micro["modelName"], $bra);
                    $ctr = new Controller($micro["controllerName"], $bra);
                    $groupResult->addMicroscope(new Microscope($mod, $ctr, $micro["rate"], $micro["desc"]));
                }
                $groupsResult[] = $groupResult;
            }

            return $groupsResult;
        }
    }