<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/../services/KeywordService.php");
    include_once(__DIR__ . "/../../utils/browser_supports_webp.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class MicroscopeService extends AbstractService {
        static private $instance;

        static function getInstance() : MicroscopeService{
            if(!isset(self::$instance))
                self::$instance = new MicroscopeService();
           
            return self::$instance;
        }

        function save(Microscope $micro) : int {
            global $pdo;
            
            $sth = $pdo->prepare("INSERT INTO microscope VALUES (NULL, :rate, :descr, :normDescr, :type, :access, :modId, :ctrId, NULL)");

            $sth->execute([
                "rate" => $micro->getRate(),
                "descr" => $micro->getDescr(),
                "normDescr" => $micro->getNormDescr(),
                "type" => $micro->getType(),
                "access" => $micro->getAccess(),
                "modId" => ModelService::getInstance()->getModelId($micro->getModel()),
                "ctrId" => ControllerService::getInstance()->getControllerId($micro->getController())
            ]);     

            $microId = $pdo->lastInsertId();
            $micro->setId($microId);

            //bind keywords
            $keywordService = KeywordService::getInstance();
            foreach ($micro->getKeywords() as $kw) {
                $keywordService->bind($kw->getId(), $microId);
            }

            return $microId;
        }

        function bind($microId, $groupId) {
            global $pdo;

            $pdo->exec("UPDATE microscope SET microscopes_group_id = $groupId WHERE id = $microId");
        }

        function findMicroscopeById($microId) {
            global $pdo;

            $sql = "
                select mi.id as microId, com.id as comId, com.name as compagnyName, bra.id as braId, bra.name as brandName, model.id as modId, model.name as modelName, ctr.id as ctrId, ctr.name as controllerName, rate, `descr`, type, access
                from microscope as mi
                join controller as ctr
                on ctr.id = mi.controller_id
                join model
                on model.id = mi.model_id
                join brand as bra
                on bra.id = model.brand_id
                join compagny as com
                on com.id = bra.compagny_id
                where mi.id = $microId
            ";

            $sth = $pdo->query($sql);
            $microInfos = $sth->fetch(PDO::FETCH_NAMED);

            $com = (new Compagny($microInfos["compagnyName"]))
                ->setId($microInfos["comId"]);
            $bra = (new Brand($microInfos["brandName"], $com))
                ->setId($microInfos["braId"]);;
            $mod = (new Model($microInfos["modelName"], $bra))
                ->setId($microInfos["modId"]);;
            $ctr = (new Controller($microInfos["controllerName"], $bra))
                ->setId($microInfos["ctrId"]);;

            $kws = KeywordService::getInstance()->findAllKeywordsByMicroscopeId($microInfos["microId"]);

            $micro = new Microscope($mod, $ctr, $microInfos["rate"], $microInfos["descr"], $microInfos["type"], $microInfos["access"], $kws);
            return $micro->setId($microId);
        }

        function findAllMicroscopesByGroupId($groupId) {
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
                $micros[$microId] = $microscopeService->findMicroscopeById($microId);
            }

            return $micros;
        }

        function findAllMicroscopes($includeLocked = true, $filters = [], int $limit = -1, int $offset = -1) {
            global $pdo;

            // get groups infos
            if(empty($filters)) {
                $sql = "
                    select m.id as microId, m.microscopes_group_id as groupId
                    from microscope as m
                ";
                if(!$includeLocked)
                    $sql .= "where m.microscopes_group_id not in (
                            select id
                            from locked_microscopes_group)";
            } else {
                $sqlFilters = implode(" AND concat REGEXP ", array_map(function ($filter) use ($pdo) { $quote = preg_quote($filter); return $pdo->quote(strNormalize($quote)); }, $filters));
                $sql = "
                    SELECT microId, groupId, concat from (select mi.id as microId, g.id as groupId, CONCAT_WS(' ', GROUP_CONCAT(DISTINCT con.norm_lastname SEPARATOR ' '), GROUP_CONCAT(DISTINCT c.norm_name SEPARATOR ' '), GROUP_CONCAT(DISTINCT norm_tag SEPARATOR ' '), LOWER(mo.name), LOWER(ctr.name), LOWER(b.name), LOWER(cmp.name), mi.norm_descr) as concat
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
                    GROUP BY mi.id) as infos
                    where concat REGEXP $sqlFilters
                ";
                if(MY_DBMS == DBMS::SQLite) {
                    str_replace(" SEPARATOR", ",", $sql);
                    str_replace("DISTINCT ", "", $sql);
                }
                if(!$includeLocked)
                    $sql .= " and microId not in (select m.id as microId
                    from locked_microscopes_group lg
                    join microscopes_group as g
                    on g.id = lg.microscopes_group_id
                    join microscope as m
                    on m.microscopes_group_id = g.id)";
            }

            $sql .= " ORDER BY microId";
            if($limit >=0) 
                $sql .= " LIMIT $limit";
            if($offset >=0) 
                $sql .= " OFFSET $offset";

            $sth = $pdo->query($sql);

            // generate micros
            $micros = [];
            $microsInfos = $sth->fetchAll(PDO::FETCH_NAMED);
            foreach ($microsInfos as $microInfos)
                $micros[$microInfos["groupId"]][$microInfos["microId"]] = $this->findMicroscopeById($microInfos["microId"]);

            return $micros;
        }

        function countAllMicroscopes($includeLocked = true, $filters = []) {
            global $pdo;

            // get groups infos
            if(empty($filters)) {
                $sql = "
                    select count(m.id) as nbMicros
                    from microscope as m
                ";
                if(!$includeLocked)
                    $sql .= "where m.microscopes_group_id not in (
                            select id
                            from locked_microscopes_group)";
            } else {
                $sqlFilters = implode(" AND concat REGEXP ", array_map(function ($filter) use ($pdo) { $quote = preg_quote($filter); return $pdo->quote(strNormalize($quote)); }, $filters));
                $sql = "
                    select count(microId) as nbMicros from (select mi.id as microId, g.id as groupId, CONCAT_WS(' ', GROUP_CONCAT(DISTINCT con.norm_lastname SEPARATOR ' '), GROUP_CONCAT(DISTINCT c.norm_name SEPARATOR ' '), GROUP_CONCAT(DISTINCT norm_tag SEPARATOR ' '), LOWER(mo.name), LOWER(ctr.name), LOWER(b.name), LOWER(cmp.name), mi.norm_descr) as concat
                    from microscopes_group as g
                    join lab
                    on lab.id = g.lab_id
                    join manage as mana
                    on mana.microscopes_group_id = g.id
                    join contact as con
                    on con.id = mana.contact_id
                    join microscope as mi
                    on mi.microscopes_group_id = g.id
                    join microscope_keyword as mk
                    on mk.microscope_id = mi.id
                    join keyword as k
                    on k.id = mk.keyword_id
                    join category as c
                    on c.id = k.category_id
                    join model as mo
                    on mo.id = mi.model_id
                    join controller as ctr
                    on ctr.id = mi.controller_id
                    join brand as b
                    on b.id = mo.brand_id
                    join compagny as cmp
                    on cmp.id = b.compagny_id
                    GROUP BY mi.id) as infos
                    where concat REGEXP $sqlFilters
                ";
                if(MY_DBMS == DBMS::SQLite) {
                    str_replace(" SEPARATOR", ",", $sql);
                    str_replace("DISTINCT ", "", $sql);
                }
                if(!$includeLocked)
                    $sql .= " and microId not in (select m.id as microId
                    from locked_microscopes_group lg
                    join microscopes_group as g
                    on g.id = lg.microscopes_group_id
                    join microscope as m
                    on m.microscopes_group_id = g.id)";
            }

            $sth = $pdo->query($sql);

            return $sth->fetch(PDO::FETCH_COLUMN);
        }

        public function delete($entity) {
            $id = $entity->getId();

            $existingImgs = glob(__DIR__ . "/../../public/img/micros/" . "$id.*");
            if($existingImgs) {
                foreach ($existingImgs as $img)
                    unlink($img);
            }

            parent::delete($entity);
        }

        function getImgPathById($microId) {
            $path = glob(__DIR__ . "/../../public/img/micros/" . "$microId.*");

            if(browserSupportsWebp())
                $ext = ".webp"; 
            else
                $ext = ".jpeg"; 

            if(!$path)
                return "/public/img/micros/default" . $ext;
            else
                return "/public/img/micros/$microId" . $ext;
        }
    }