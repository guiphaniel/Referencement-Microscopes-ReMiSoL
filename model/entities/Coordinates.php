<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    
    class Coordinates extends AbstractEntity  {
        private $lat;
        private $lon;

        function __construct(float $lat, float $lon) {
            $this->lat = $lat;
            $this->lon = $lon;
        }

        public function getLat() { return $this->lat; }

        public function setLat($lat) {
            $this->lat = $lat;

            return $this;
        }   

        public function getLon() { return $this->lon; }

        public function setLon($lon)
        {
            $this->lon = $lon;

            return $this;
        }
    }