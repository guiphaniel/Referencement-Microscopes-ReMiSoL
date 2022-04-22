<?php
    include_once(__DIR__ . "/Microscope.php");
    include_once(__DIR__ . "/Coordinates.php");
    include_once(__DIR__ . "/Contact.php");
    include_once(__DIR__ . "/Lab.php");

    class MicroscopesGroup extends AbstractEntity {
        private array $microscopes;

        function __construct(private Coordinates $coor, private Lab $lab, private Contact $contact) {}

        public function getMicroscopes() : array
        {
            return $this->microscopes;
        }

        function addMicroscope(Microscope $microscope) {
            $this->microscopes[] = $microscope;
        }

        function removeMicroscope(Microscope $microscope) {
            unset($microscopes[array_search($microscope, $this->microscopes, true)]);
        }

        public function getCoor()
        {
                return $this->coor;
        }

        public function setCoor($coor)
        {
                $this->coor = $coor;

                return $this;
        }

        public function getContact() : Contact
        {
            return $this->contact;
        }

        public function setContact(Contact $contact)
        {
            $this->contact = $contact;

            return $this;
        }

        public function getLab() : Lab
        {
            return $this->lab;
        }

        public function setLab(Lab $lab)
        {
            $this->lab = $lab;

            return $this;
        }
    }