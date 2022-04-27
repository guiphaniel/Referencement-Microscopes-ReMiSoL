<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/ModelService.php");
    include_once(__DIR__ . "/ControllerService.php");
    include_once(__DIR__ . "/KeywordService.php");

    class MicroscopeService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
                self::$instance = new MicroscopeService();
           
            return self::$instance;
        }

        function add(int $groupId, Microscope $micro) : int {
            global $pdo;
            
            $sth = $pdo->prepare("INSERT INTO microscope VALUES (NULL, :rate, :desc, :access, :modId, :ctrId, :groupId)");

            $sth->execute([
                "rate" => $micro->getRate(),
                "desc" => $micro->getDesc(),
                "access" => $micro->getAccess(),
                "modId" => ModelService::getInstance()->getModelId($micro->getModel()),
                "ctrId" => ControllerService::getInstance()->getControllerId($micro->getController()),
                "groupId" => $groupId
            ]);     

            $microId = $pdo->lastInsertId();

            //bind keywords
            foreach ($micro->getKeywords() as $cat => $tags) {
                foreach ($tags as $tag) {
                    $sth = $pdo->prepare("INSERT INTO microscope_keyword VALUES (:microId, :kwId)");

                    $sth->execute([
                        "microId" => $microId,
                        "kwId" => KeywordService::getInstance()->getKeywordId($cat, $tag)
                    ]);
                }
            }

            return $microId;
        }

        function findAllKeywords($microId) {
            global $pdo;

            $sql = "
                select cat, tag
                from microscope as mi
                join microscope_keyword as mk
                on mk.microscope_id = mi.id
                join keyword as k
                on k.id = mk.keyword_id
                where mk.microscope_id = $microId
            ";

            $sth = $pdo->query($sql);
            $keywords = $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_COLUMN);

            return $keywords;
        }

        function findMicroscope($microId) {
            global $pdo;

            $sql = "
                select mi.id as microId, com_name as compagnyName, bra_name as brandName, mod_name as modelName, ctr_name as controllerName, rate, desc, access
                from microscope as mi
                join controller as ctr
                on ctr.id = mi.controller_id
                join model as mod
                on mod.id = mi.model_id
                join brand as bra
                on bra.id = mod.brand_id
                join compagny as com
                on com.id = bra.compagny_id
                where microId = $microId
            ";

            $sth = $pdo->query($sql);
            $microInfos = $sth->fetch(PDO::FETCH_NAMED);

            $com = new Compagny($microInfos["compagnyName"]);
            $bra = new Brand($microInfos["brandName"], $com);
            $mod = new Model($microInfos["modelName"], $bra);
            $ctr = new Controller($microInfos["controllerName"], $bra);

            $kws = $this->findAllKeywords($microInfos["microId"]);

            return new Microscope($mod, $ctr, $microInfos["rate"], $microInfos["desc"], $microInfos["access"], $kws);
        }
    }