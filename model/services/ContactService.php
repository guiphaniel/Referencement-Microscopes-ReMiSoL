<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Contact.php");

    class ContactService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
            self::$instance = new ContactService();
           
            return self::$instance;
        }

        function getContactId(Contact $contact) {
            global $pdo;
    
            $sth = $pdo->prepare("SELECT id FROM contact where name = :name and email = :email");
    
            $sth->execute([
                "name" => $contact->getName(),
                "email" => $contact->getEmail()
            ]);
    
            $row = $sth->fetch();

            // if this contact exists, reutrn its id, else reutrn -1
            return $row ? $row[0] : -1;
        }
    
        function save(Contact $contact) {
            global $pdo;

            // if the contact is not yet in the db, add it
            $id = self::getContactId($contact);
            if ($id < 0) {
                $sth = $pdo->prepare("INSERT INTO contact VALUES (NULL, :name, :email");
        
                $sth->execute([
                    "name" => $contact->getName(),
                    "email" => $contact->getEmail()
                ]);
            } else { //else, update it
                $sth = $pdo->prepare("UPDATE contact SET name = :name, email = :email WHERE id = :id");
        
                $sth->execute([
                    "name" => $contact->getName(),
                    "email" => $contact->getEmail(),
                    "id" => $id
                ]);
            }  
        }    
    }

