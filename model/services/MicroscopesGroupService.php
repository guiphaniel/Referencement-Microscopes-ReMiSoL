<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/../entities/User.php");
    include_once(__DIR__ . "/../services/ContactService.php");
    include_once(__DIR__ . "/../../utils/normalize_utf8_string.php");
    include_once(__DIR__ . "/../../utils/send_email.php");
    
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class MicroscopesGroupService extends AbstractService {
        static private $instance;

        static function getInstance() : MicroscopesGroupService {
            if(!isset(self::$instance))
                self::$instance = new MicroscopesGroupService();
           
            return self::$instance;
        }

        function save(MicroscopesGroup $group) : int {
            global $pdo;

            //save the coordinates
            $coorId = CoordinatesService::getInstance()->save($group->getCoor());

            // save the lab
            $labId = LabService::getInstance()->save($group->getLab());

            // save the group and bind it to the lab and it's user (owner)
            $sth = $pdo->prepare("INSERT INTO microscopes_group VALUES (NULL, :coorId, :labId, :userId)");

            try {
                $sth->execute([
                    "coorId" => $coorId,
                    "labId" => $labId,
                    "userId" => $_SESSION["user"]["id"]
                ]);
            } catch (\Throwable $th) {
                if(str_contains($th->getMessage() ,"UNIQUE constraint failed"))
                    throw new Exception("Un groupe de microscopes existe déjà à cet emplacement.");
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
            foreach($group->getMicroscopes() as $micro) {
                $microscopeService = MicroscopeService::getInstance();
                $micro->setId($microscopeService->save($micro));
                $microscopeService->bind($micro->getId(), $groupId);
            }

            $group->setId($groupId);

            // lock / unlock the group
            $method = $group->isLocked() ? "" : "un" . "lock";
            $this->$method($group);

            return $groupId;
        }

        public function findGroupOwner(MicroscopesGroup | int $group) {
            global $pdo;

            if(is_int($group))
                $groupId = $group;
            else
                $groupId = $group->getId();

            $sth = $pdo->query("SELECT user_id FROM microscopes_group WHERE id = $groupId");

            $row = $sth->fetch();

            // if this group exists and a user is bound to it, return the user, else null
            $user = null;
            if($row && $row["user_id"] != null)
                $user = UserService::getInstance()->findUserById($row["user_id"]);

            return $user;
        }

        public function findGroupOwnerByGroupId(int $groupId) {
            global $pdo;

            $sth = $pdo->query("SELECT user_id FROM microscopes_group WHERE id = $groupId");

            $row = $sth->fetch();

            // if this group exists and a user is bound to it, return the user, else null
            $user = null;
            if($row && $row["user_id"] != null)
                $user = UserService::getInstance()->findUserById($row["user_id"]);

            return $user;
        }

        function findAllMicroscopesGroup($includeLocked = true, $filters = [], int $limit = -1, int $offset = -1) {
            global $pdo;

            // get groups infos
            if(empty($filters)) {
                $sql = "
                    select g.id as groupId
                    from microscopes_group as g
                ";
                if(!$includeLocked)
                    $sql .= "where g.id not in (select microscopes_group_id from locked_microscopes_group)";
            } else {
                $sqlFilters = implode("|", array_map(function ($filter) { $quote = preg_quote($filter); return strNormalize($quote); }, $filters));
                $sqlFilters = $pdo->quote($sqlFilters);
                $sql = "
                    SELECT groupId, coorId, labId, microId, concat from (
                        select g.id as groupId, coordinates_id as coorId, lab.id as labId, mi.id as microId, CONCAT_WS(' ', GROUP_CONCAT(DISTINCT con.norm_lastname SEPARATOR ' '), GROUP_CONCAT(DISTINCT c.norm_name), GROUP_CONCAT(DISTINCT norm_tag), LOWER(mo.name),  LOWER(ctr.name), LOWER(b.name), LOWER(cmp.name), mi.norm_descr) as concat
                        from microscopes_group as g
                        join lab
                        on lab.id = g.lab_id
                        join manage as mana
                        on mana.microscopes_group_id = g.id
                        join contact as con
                        on con.id = mana.contact_id
                        join microscope as mi
                        on mi.microscopes_group_id = g.id
                        left join microscope_keyword as mk
                        on mk.microscope_id = mi.id
                        left join keyword as k
                        on k.id = mk.keyword_id
                        left join category as c
                        on c.id = k.category_id
                        join model as mo
                        on mo.id = mi.model_id
                        join controller as ctr
                        on ctr.id = mi.controller_id
                        join brand as b
                        on b.id = mo.brand_id
                        join compagny as cmp
                        on cmp.id = b.compagny_id
                        GROUP BY mi.id
                    ) as groupsInfos where concat REGEXP $sqlFilters
                ";
                if(MY_DBMS == DBMS::SQLite) {
                    $sql = str_replace(" SEPARATOR", ",", $sql);
                    $sql = str_replace("DISTINCT ", "", $sql);
                }
                if(!$includeLocked)
                    $sql .= "and groupId not in (select microscopes_group_id from locked_microscopes_group)";
            }

            $sql .= " ORDER BY groupId";
            if($limit >=0) 
                $sql .= " LIMIT $limit";
            if($offset >=0) 
                $sql .= " OFFSET $offset";

            $sth = $pdo->query($sql);

            // generate groups
            $groups = [];
            if(empty($filters)) {
                $groupsIds = $sth->fetchAll(PDO::FETCH_COLUMN);
                foreach ($groupsIds as $groupId)
                    $groups[$groupId] = $this->findMicroscopesGroupById($groupId);
            } else {
                $groupsInfos = $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_NAMED);
                foreach ($groupsInfos as $groupId => $groupInfos) {
                    $coor = CoordinatesService::getInstance()->findCoordinatesById($groupInfos[0]["coorId"]);
                    $lab = LabService::getInstance()->findLabById($groupInfos[0]["labId"]);
                    $contacts = ContactService::getInstance()->findAllContactsByGroupId($groupId);

                    $group = new MicroscopesGroup($coor, $lab, $contacts);

                    $microscopeService = MicroscopeService::getInstance();
                    foreach ($groupInfos as $infos) {
                        $micro = $microscopeService->findMicroscopeById($infos["microId"]);
                        $group->addMicroscope($micro);
                    }
                    $groups[$groupId] = $group->setId($groupId);
                }
            }

            return $groups;
        }

        function findAllMicroscopesGroupByOwner(int | User $user, $includeLocked = true)
        {
            global $pdo;

            if (is_int($user))
                $userId = $user;
            else
                $userId = $user->getId();

            // get groups infos
            $sql = "
                select id, coordinates_id, lab_id
                from microscopes_group as g
                where user_id = $userId
            ";
            if (!$includeLocked)
                $sql .= " and id not in (select microscopes_group_id from locked_microscopes_group)";
            $sth = $pdo->query($sql);
            $groupsInfos = $sth->fetchAll(PDO::FETCH_NAMED);

            // generate groups
            $groups = [];
            foreach ($groupsInfos as $groupInfos) {
                $groupId = $groupInfos["id"];

                $coor = CoordinatesService::getInstance()->findCoordinatesById($groupInfos["coordinates_id"]);
                $lab = LabService::getInstance()->findLabById($groupInfos["lab_id"]);
                $contacts = ContactService::getInstance()->findAllContactsByGroupId($groupId);
                $micros = MicroscopeService::getInstance()->findAllMicroscopesByGroupId($groupId);

                $group = new MicroscopesGroup($coor, $lab, $contacts);

                $group->setId($groupId);

                $group->setMicroscopes($micros);

                $group->setLocked($this->isLocked($group));

                $groups[$groupId] = $group;
            }

            return $groups;
        }

        function findMicroscopesGroupById(int $groupId) {
            global $pdo;

            // get groups infos
            $sql = "
                select g.id, coordinates_id, lab_id
                from microscopes_group as g
                where g.id = $groupId
            ";
            $sth = $pdo->query($sql);
            $groupInfos = $sth->fetch(PDO::FETCH_NAMED);

            // if the group doesn't exist, return null
            if(!$groupInfos)
                return null;

            // generate the group
            $coor = CoordinatesService::getInstance()->findCoordinatesById($groupInfos["coordinates_id"]);
            $lab = LabService::getInstance()->findLabById($groupInfos["lab_id"]);
            $contacts = ContactService::getInstance()->findAllContactsByGroupId($groupId);
            $micros = MicroscopeService::getInstance()->findAllMicroscopesByGroupId($groupId);

            $group = new MicroscopesGroup($coor, $lab, $contacts);

            $group->setMicroscopes($micros);

            $group->setId($groupId);

            $group->setLocked($this->isLocked($group));

            return $group->setId($groupId);
        }

        private function groupToId(int | MicroscopesGroup $group) {
            if(is_int($group))
                return $group;
            
            return $group->getId();
        }

        function isLocked(int | MicroscopesGroup $group) {
            global $pdo;

            $id = $this->groupToId($group);

            $sth = $pdo->query("SELECT COUNT(microscopes_group_id) as is_locked FROM locked_microscopes_group WHERE microscopes_group_id = $id");

            return $sth->fetch()["is_locked"] ? true : false;
        }

        function lock(int | MicroscopesGroup $group) {
            if($this->isLocked($group))
                return;

            global $pdo;

            $id = $this->groupToId($group);
            $pdo->exec("INSERT INTO locked_microscopes_group VALUES($id)");
        }

        function unlock(int | MicroscopesGroup $group) {
            if(!$this->isLocked($group))
                return;

            global $pdo;

            $id = $this->groupToId($group);
            $pdo->exec("DELETE FROM locked_microscopes_group WHERE microscopes_group_id = $id");

            $owner = $this->findGroupOwner($group);

            $subject = "[RéMiSoL] Validation de votre fiche";
        
            $content = "Bonjour,\n\nNous avons le plaisir de vous informer que votre fiche a été validée ! Elle est désormais visible pour tous les visiteurs du site.\n\nA bientôt,\n\nL'équipe de " . WEBSITE_URL;

            sendEmail($owner->getEmail(), $subject, $content);
        }

        //override so images and coordinates (1-1) are deleted too
        function delete($group) {
            $microService = MicroscopeService::getInstance();
            foreach($group->getMicroscopes() as $micro)
                $microService->delete($micro);

            CoordinatesService::getInstance()->delete($group->getCoor());

            parent::delete($group);
        }
    }   