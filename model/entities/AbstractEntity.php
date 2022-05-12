<?php
    abstract class AbstractEntity implements JsonSerializable
    {
        private int $id;

        function __construct()
        {
            $this->id = -1;
        }

        public function getId() : int
        {
            return $this->id;
        }

        function setId(int $id) {
            $this->id = $id;

            return $this;
        }
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
    