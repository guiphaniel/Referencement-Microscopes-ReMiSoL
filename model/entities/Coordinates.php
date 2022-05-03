<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    
    class Coordinates extends AbstractEntity  {
        private $lat;
        private $lon;

        function __construct(float $lat, float $lon) {
            $this->setLat($lat);
            $this->setLon($lon);
        }

        public function getLat() { return $this->lat; }

        public function setLat($lat) {
            if ($lat < 42 || $lat > 52)
                throw new ErrorException("La latitude renseignée dépasse les valeurs acceptables.");
            
                $this->lat = $lat;

            return $this;
        }   

        public function getLon() { return $this->lon; }

        public function setLon($lon)
        {
            if ($lon < -6 || $lon > 11)
                throw new ErrorException("La longitude renseignée dépasse les valeurs acceptables.");

            $this->lon = $lon;

            return $this;
        }
    }