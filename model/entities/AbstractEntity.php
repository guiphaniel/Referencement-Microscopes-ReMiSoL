<?php
    abstract class AbstractEntity implements JsonSerializable
    {
        // must override this method to serialize the private attributes too
        public function jsonSerialize() : mixed {
            $reflector = new ReflectionClass(static::class);
            $properties = $reflector->getProperties();
            
            $json = [];
            foreach($properties as $property) {
                $json[$property->getName()] = $property->getValue($this);
            }
            
            return $json;
        }
    }
    