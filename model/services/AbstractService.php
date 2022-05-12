<?php
    include_once(__DIR__ . "/../start_db.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class AbstractService {
        static private $instance;

        public static function getInstance() {
            if(!isset(self::$instance))
            $class = static::class;
                self::$instance = new $class();
           
            return self::$instance;
        }

        protected function getProperties($entity) {
            $properties = (new ReflectionClass(get_class($entity)))->getProperties();
            foreach($properties as $property)
                $result[$property->getName()] = (object)["type" => $property->getType()->getName(), "value" => $property->getValue($entity)];

            return $result??[];
        }

        protected function isEntity($className) {
            $class = new ReflectionClass($className);
            while ($parent = $class->getParentClass()) {
                if ($parent->getName() == "AbstractEntity")
                    return true;
            }

            return false;
        }

        //TODO: faire en sorte que les attributs des classes correspondent à ceux de la base de données (name...)
        public function update(AbstractEntity $old, AbstractEntity $new) {
            global $pdo;

            $oldProperties = $this->getProperties($old);
            $newProperties = $this->getProperties($new);
            
            $toUpdate = array_diff($newProperties, $oldProperties);

            $updateSql = "UPDATE TABLE " . strtolower(get_class($old)) . " SET ";
            $toUpdateSqlFields = [];

            foreach ($toUpdate as $name => $property) {
                if(is_array($property)) { // assume that arrays will always be arrays of entities
                    $this->updateEntities($oldProperties[$name]->value, $property);
                } elseif ($this->isEntity($property->type)) {
                    $this->update($oldProperties[$name]->value, $property->value);
                } else { // primitive type
                    $toUpdateSqlFields[] = $pdo->quote("$name = '$property->value'");
                }
            }

            if(empty($toUpdateSqlFields))
                return;

            $updateSql .= implode(", ", $toUpdateSqlFields);
            $id = $old->getId();
            $updateSql .= "WHERE id = $id";

            $pdo->exec($updateSql);
        }
        
        protected function updateEntities($oldEntities, $newEntities) {
            $callback = [$this, 'haveSameId'];
            $toInsert = array_udiff($newEntities, $oldEntities, $callback);
            $toUpdate = array_uintersect($newEntities, $oldEntities, $callback);
            $toDelete = array_udiff($oldEntities, $newEntities, $callback);

            foreach ($toInsert as $entity) {
                $service = get_class($entity) . "Service";
                $service::getInstance()->save($entity);
            }

            foreach ($toUpdate as $entity) {
                $service = get_class($entity) . "Service";
                $service::getInstance()->update($entity);
            }

            foreach ($toDelete as $entity) {
                $service = get_class($entity) . "Service";
                $service::getInstance()->delete($entity);
            }
        }
    
        protected function haveSameId($a, $b) {
            return $a->getId() - $b->getId();
        }

        protected function delete($entity) {
            global $pdo;
            $table = strtolower(get_class($entity));
            $id = $entity->getId();

            $pdo->exec("DELETE FROM $table WHERE id = $id");
        }
    }           
    
