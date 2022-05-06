<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Model.php");
    include_once(__DIR__ . "/../services/BrandService.php");

    class ModelService {
        static private $instance;

        private function __construct() {}

        static function getInstance() : ModelService {
            if(!isset(self::$instance))
                self::$instance = new ModelService();
           
            return self::$instance;
        }

        function getModelId(Model $model) {
            global $pdo;

            // there is no need to check for the brand and compagny, as mod_name is unique
            $sth = $pdo->prepare("SELECT id FROM model where mod_name = :name");

            $sth->execute([
                "name" => $model->getName()
            ]);

            $row = $sth->fetch();

            // if this model exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        /** Saves the model if it doesn't exist yet, and returns its id */
        function save(Model $model) {
            global $pdo;

            $id = $this->getModelId($model);

            // if the model isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO model VALUES (NULL, :name, :brandId)");
        
                $sth->execute([
                    "name" => $model->getName(),
                    "brandId" => BrandService::getInstance()->getBrandId($model->getBrand())
                ]);
                
                $id = $pdo->lastInsertId();
            }          

            return $id;
        }   

        function getAllModels(Brand $brand) : array {
            global $pdo;
            $models = [];
            
            $sth = $pdo->prepare("SELECT mod_name FROM model where brand_id = :brandId");

            $sth->execute([
                "brandId" => BrandService::getInstance()->getBrandId($brand)
            ]);

            $names = $sth->fetchAll(PDO::FETCH_COLUMN);

            foreach ($names as $name) {
                $models[] = new Model($name, $brand);
            }

            return $models;
        }
    }            