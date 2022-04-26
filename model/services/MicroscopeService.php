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
            
            $sth = $pdo->prepare("INSERT INTO microscope VALUES (NULL, :rate, :desc, :modId, :ctrId, :groupId)");

            $sth->execute([
                "rate" => $micro->getRate(),
                "desc" => $micro->getDesc(),
                "modId" => ModelService::getInstance()->getModelId($micro->getModel()),
                "ctrId" => ControllerService::getInstance()->getControllerId($micro->getController()),
                "groupId" => $groupId
            ]);     

            $microId = $pdo->lastInsertId();

            //bind keywords
            foreach ($micro->getKeywords() as $kw) {
                $sth = $pdo->prepare("INSERT INTO microscope_keyword VALUES (:microId, :kwId)");

                $sth->execute([
                    "microId" => $microId,
                    "kwId" => KeywordService::getInstance()->getKeywordId($kw)
                ]);
            }

            return $microId;
        }
    }