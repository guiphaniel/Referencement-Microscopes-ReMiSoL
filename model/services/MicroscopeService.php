<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/../entities/Keyword.php");

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
            
            $sth = $pdo->prepare("INSERT INTO microscope VALUES (NULL, :rate, :desc, :type, :access, :modId, :ctrId, NULL)");

            $sth->execute([
                "rate" => $micro->getRate(),
                "desc" => $micro->getDesc(),
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

        function findAllKeywords($microId) {
            global $pdo;

            $sql = "
                select cat, k.id, tag
                from microscope as mi
                join microscope_keyword as mk
                on mk.microscope_id = mi.id
                join keyword as k
                on k.id = mk.keyword_id
                where mk.microscope_id = $microId
            ";

            $sth = $pdo->query($sql);
            $keywords = $sth->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_NAMED);

            $kws = [];

            foreach($keywords as $cat => $infos) {
                foreach ($infos as $info) {
                    $kws[] = (new Keyword($cat, $info["tag"]))->setId($info["id"]);
                }
            }

            return $kws;
        }

        function findMicroscopeById($microId) {
            global $pdo;

            $sql = "
                select mi.id as microId, com.id as comId, com.name as compagnyName, bra.id as braId, bra.name as brandName, mod.id as modId, mod.name as modelName, ctr.id as ctrId, ctr.name as controllerName, rate, desc, type, access
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

            $com = (new Compagny($microInfos["compagnyName"]))
                ->setId($microInfos["comId"]);
            $bra = (new Brand($microInfos["brandName"], $com))
                ->setId($microInfos["braId"]);;
            $mod = (new Model($microInfos["modelName"], $bra))
                ->setId($microInfos["modId"]);;
            $ctr = (new Controller($microInfos["controllerName"], $bra))
                ->setId($microInfos["ctrId"]);;

            $kws = $this->findAllKeywords($microInfos["microId"]);

            $micro = new Microscope($mod, $ctr, $microInfos["rate"], $microInfos["desc"], $microInfos["type"], $microInfos["access"], $kws);
            return $micro->setId($microId);
        }

        protected function delete($entity) {
            $id = $entity->getId();

            $existingImgs = glob(__DIR__ . "/../../public/img/micros/" . "$id.*");
            if($existingImgs) {
                foreach ($existingImgs as $img)
                    unlink($img);
            }

            parent::delete($entity);
        }
    }