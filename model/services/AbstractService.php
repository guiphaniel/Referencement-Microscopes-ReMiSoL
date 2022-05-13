<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../../utils/camel_case_to_snake_case.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    abstract class AbstractService {
        static private $instance;

        public abstract static function getInstance();

        protected function getProperties($entity) {
            $properties = (new ReflectionClass(get_class($entity)))->getProperties();
            foreach($properties as $property)
                $result[$property->getName()] = $property->getValue($entity);

            return $result??[];
        }

        protected function isEntity($entity) {
            $class = new ReflectionClass($entity);
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
            
            $toUpdate = $this->propArrayDiff($newProperties, $oldProperties);

            $updateSql = "UPDATE " . camelCaseToSnakeCase(get_class($old)) . " SET ";
            $toUpdateSqlFields = [];

            foreach ($toUpdate as $name => $property) {
                if(is_array($property)) { // assume that arrays will always be arrays of entities
                    $this->updateEntities($old->getId(), $oldProperties[$name], $property);
                } elseif (is_object($property)) {
                    $this->update($oldProperties[$name], $property);
                } else { // primitive type
                    $name = camelCaseToSnakeCase($name);
                    $value = $pdo->quote($property);
                    $toUpdateSqlFields[] = "$name = $value";
                }
            }

            if(empty($toUpdateSqlFields))
                return;

            $updateSql .= implode(", ", $toUpdateSqlFields);
            $id = $old->getId();
            $updateSql .= " WHERE id = $id";

            $pdo->exec($updateSql);
        }

        private function propArrayDiff($a, $b) {
            $diff = [];
            foreach($a as $name => $value) {
                if($name === "id") continue;

                if(!$this->areEqual($value, $b[$name]))
                    $diff[$name] = $value;
            }
            
            return $diff;
        }

        private function areEqual($a, $b) {
            // arrays
            if(is_array($a)) { // assumes that arrays are always arrays of entities
                if(count($a) != count($b))
                    return false;

                $equal = true;
                foreach($a as $key => $entity) {
                    if(!$this->areEqual($entity, $b[$key])) {
                        $equal = false;
                        break;
                    }
                }
                return $equal;
            }

            // primitive type
            if(!is_object($a))
                return $a === $b;

            // entities
            $refA = new ReflectionClass($a);
            $refB = new ReflectionClass($b);
        
            $aProps = $refA->getProperties();
        
            foreach ($aProps as $aProp) {
                $propName = $aProp->getName();

                if ($propName === "id") continue;
                $aVal = $aProp->getValue($a);
        
                $bProp = $refB->getProperty($aProp->getName());
                $bVal = $bProp->getValue($b);
                if (!$this->areEqual($aVal, $bVal)) {
                    return false;
                }
            }
            return true;
        }
        
        /**update entities in arrays of entities */
        protected function updateEntities($parentId, $oldEntities, $newEntities) {
            $callback = [$this, 'haveSameId'];
            $toInsert = array_udiff($newEntities, $oldEntities, $callback);
            $toUpdate = array_uintersect($newEntities, $oldEntities, $callback);
            $toDelete = array_udiff($oldEntities, $newEntities, $callback);

            foreach ($toInsert as $entity) {
                $class = get_class($entity);
                if($class == "Contact" || $class == "Keyword" || $class == "Model" || $class == "Controller") { // those classes are aggregations, so we have to check if they already existed in the db to bind them if necessary
                    $service = (get_class($entity) . "Service")::getInstance();
                    $method = "get" . $class . "Id";
                    $id = call_user_func_array(array($service, $method), array($entity));

                    // if the entity is already in the database, bind the parent entity to it (as it is aggregation). Else, create it.
                    if($id != -1) {
                        $service->bind($id, $parentId);
                        return;
                    } else {
                        $id = $service->save($entity);
                        $service->bind($id, $parentId);
                        return;
                    }
                }

                $service = (get_class($entity) . "Service")::getInstance();
                $id = $service->save($entity);

                if($class == "Microscope") // microscope is a composition (with groups), so we need to bind it to the group
                    $service->bind($id, $parentId);
            }

            foreach ($toUpdate as $entity) {
                $oldEntity = array_values(array_filter($oldEntities, function ($old) use (&$entity) {return $old->getId() == $entity->getId();}))[0];

                $this->update($oldEntity, $entity);
            }

            foreach ($toDelete as $entity) {
                $class = get_class($entity);
                if($class == "Contact" || $class == "Keyword" || $class == "Model" || $class == "Controller") { // those classes are aggregations...
                    $service = (get_class($entity) . "Service")::getInstance();
                    $service->unbind($entity->getId(), $parentId);
                    return;
                }
                $service = get_class($entity) . "Service";
                $service::getInstance()->delete($entity);
            }
        }
    
        protected function haveSameId($a, $b) : int {
            return $a->getId() - $b->getId();   
        }

        protected function delete($entity) {
            global $pdo;
            $table = strtolower(get_class($entity));
            $id = $entity->getId();

            $pdo->exec("DELETE FROM $table WHERE id = $id");
        }
    }           
    
