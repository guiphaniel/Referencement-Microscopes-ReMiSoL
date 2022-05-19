<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Model.php");
    
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class ModelService extends AbstractService {
        static private $instance;

        static function getInstance() : ModelService {
            if(!isset(self::$instance))
                self::$instance = new ModelService();
           
            return self::$instance;
        }

        function getModelId(Model $model) {
            global $pdo;

            // there is no need to check for the brand and compagny, as name is unique
            $sth = $pdo->prepare("SELECT id FROM model where name = :name");

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
                $model->setId($id);
            }          

            return $id;
        }   

        //override : only admin can update models
        public function update(AbstractEntity $old, AbstractEntity $new) {
            if($_SESSION["user"]["admin"])
                parent::update($old, $new);
        }

        function getAllModels(Brand $brand) : array {
            global $pdo;
            $models = [];
            
            $sth = $pdo->prepare("SELECT id, name FROM model where brand_id = :brandId");

            $sth->execute([
                "brandId" => BrandService::getInstance()->getBrandId($brand)
            ]);

            $infos = $sth->fetchAll(PDO::FETCH_NAMED);

            foreach ($infos as $info) {
                $id = $info["id"];
                $models[$id] = (new Model($info["name"], $brand))
                    ->setId($id);
            }

            return $models;
        }

        function bind($modelId, $microId) {
            global $pdo;

            $pdo->exec("UPDATE microscope SET model_id = $modelId where id = $microId");
        }

        function unbind($modelId, $microId) {
            global $pdo;

            $pdo->exec("UPDATE microscope SET model_id = NULL where id = $microId");
        }
    }            