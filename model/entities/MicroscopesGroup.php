<?php
    include_once(__DIR__ . "/Microscope.php");
    include_once(__DIR__ . "/Coordinates.php");
    include_once(__DIR__ . "/Contact.php");
    include_once(__DIR__ . "/Lab.php");

    class MicroscopesGroup extends AbstractEntity {
        private array $microscopes;
        private bool $locked;

        function __construct(private Coordinates $coor, private Lab $lab, private array $contacts) {
            $this->locked = false;
            parent::__construct();
        }

        public function getMicroscopes() : array
        {
            return $this->microscopes;
        }
        
        public function setMicroscopes($microscopes)
        {
            $this->microscopes = $microscopes;

            return $this;
        }

        function addMicroscope(Microscope $microscope) {
            $this->microscopes[$microscope->getId()] = $microscope;
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

        public function getContacts() : array
        {
            return $this->contacts;
        }

        public function setContacts(array $contacts)
        {
            $this->contacts = $contacts;

            return $this;
        }

        function addContact(Contact $contact) {
            $this->contacts[] = $contact;
        }

        function removeContact(Contact $contact) {
            unset($this->contacts[array_search($contact, $this->contacts, true)]);
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

        public function isLocked()
        {
            return $this->locked;
        }

        public function setLocked($locked)
        {
            $this->locked = $locked;

            return $this;
        }
    }