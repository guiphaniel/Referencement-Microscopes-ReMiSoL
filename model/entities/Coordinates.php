<?php
    include_once(__DIR__ . "/AbstractEntity.php");
    
    class Coordinates extends AbstractEntity  {
        private $lat;
        private $lon;

        function __construct(float $lat, float $lon) {
            parent::__construct();
            $this->setLat($lat);
            $this->setLon($lon);
        }

        public function getLat() { return number_format($this->lat, 5); }

        public function setLat($lat) {
            if(!is_numeric($lat))
                throw new ErrorException("La latitude doit être de type numérique.");
            if ($lat < 41 || $lat > 52)
                throw new ErrorException("La latitude renseignée dépasse les valeurs acceptables.");
            
                $this->lat = $lat;

            return $this;
        }   

        public function getLon() { return number_format($this->lon, 5); }

        public function setLon($lon)
        {
            if(!is_numeric($lon))
                throw new ErrorException("La longitude doit être de type numérique.");
            if ($lon < -6 || $lon > 11)
                throw new ErrorException("La longitude renseignée dépasse les valeurs acceptables.");

            $this->lon = $lon;

            return $this;
        }
    }