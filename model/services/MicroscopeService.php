<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/MicroscopesGroup.php");
    include_once(__DIR__ . "/../services/KeywordService.php");

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

        public function delete($entity) {
            $id = $entity->getId();

            $existingImgs = glob(__DIR__ . "/../../public/img/micros/" . "$id.*");
            if($existingImgs) {
                foreach ($existingImgs as $img)
                    unlink($img);
            }

            parent::delete($entity);
        }
    }