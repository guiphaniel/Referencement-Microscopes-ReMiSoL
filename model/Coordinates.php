<?php
    // needs to implement JsonSerializable because fields are private
    class Coordinates implements JsonSerializable {
        private $lat;
        private $lon;

        function __construct(float $lat, float $lon) {
            $this->lat = $lat;
            $this->lon = $lon;
        }

        public function jsonSerialize() : mixed {
            return [
                'lat' => $this->lat,
                'lon' => $this->lon
            ];
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