<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/../entities/User.php");
    include_once(__DIR__ . "/MicroscopeService.php");
    include_once(__DIR__ . "/LabService.php");
    include_once(__DIR__ . "/ContactService.php");
    include_once(__DIR__ . "/UserService.php");

    class MicroscopesGroupService {
        static private $instance;

        private function __construct() {}

        static function getInstance() : MicroscopesGroupService {
            if(!isset(self::$instance))
                self::$instance = new MicroscopesGroupService();
           
            return self::$instance;
        }

        // TODO: check if the lab / brand / controller / are already in db, else add them but also add them in a table "to_verify", maybe in the add/save functions of each Service
        function save(MicroscopesGroup $group) : int {
            global $pdo;

            // save the lab
            LabService::getInstance()->save($group->getLab());

            // save the group and bind it to the lab and it's user (owner)
            $sth = $pdo->prepare("INSERT INTO microscopes_group VALUES (NULL, :lat, :lon, :labId, :userId)");

            try {
                $sth->execute([
                    "lat" => $group->getCoor()->getLat(),
                    "lon" => $group->getCoor()->getLon(),
                    "labId" => LabService::getInstance()->getLabId($group->getLab()),
                    "userId" => $_SESSION["user"]["id"]
                ]);
            } catch (\Throwable $th) {
                if(str_contains($th->getMessage() ,"UNIQUE constraint failed"))
                    throw new Exception("Un groupe de microscopes existe déjà à cet emplacement");
                else
                    throw $th;
            }
            
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
                $micro->setId(MicroscopeService::getInstance()->save($groupId, $micro));

            $group->setId($groupId);

            return $groupId;
        }

        public function findGroupOwner(MicroscopesGroup $group) {
            global $pdo;

            $groupId = $group->getId();
            $sth = $pdo->query("SELECT u.id FROM own join user as u on u.id = own.user_id where microscopes_group_id = $groupId");

            $row = $sth->fetch();

            // if this user exists, return it, else return null
            return $row ? UserService::getInstance()->findUserById($row[0]) : null;
        }

        function findAllMicroscopesGroup() {
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
                select c.id, firstname, lastname, role, email, phone_code, phone_num
                from contact as c
                join manage as m
                on m.contact_id = c.id
                where microscopes_group_id = $groupId
            ";

            $sth = $pdo->query($sql);
            $contactsInfos = $sth->fetchAll(PDO::FETCH_NAMED);

            $contacts = [];
            foreach ($contactsInfos as $contactInfos) {
                $contacts[] = (new Contact($contactInfos["firstname"], $contactInfos["lastname"], $contactInfos["role"], $contactInfos["email"], $contactInfos["phone_code"], $contactInfos["phone_num"]))
                    ->setId($contactInfos["id"]);
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
                $micros[] = $microscopeService->findMicroscopeById($microId);
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