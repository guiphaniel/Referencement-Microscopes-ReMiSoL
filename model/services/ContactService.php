<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Contact.php");

    class ContactService {
        static private $instance;

        private function __construct() {}

        static function getInstance() : ContactService {
            if(!isset(self::$instance))
            self::$instance = new ContactService();
           
            return self::$instance;
        }

        function getContactId(Contact $contact) {
            global $pdo;
    
            $sth = $pdo->prepare("SELECT id FROM contact where firstname = :firstname and lastname = :lastname and role = :role and email = :email and phone_code = :phoneCode and phone_num = :phoneNum");
    
            $sth->execute([
                "firstname" => $contact->getFirstname(),
                "lastname" => $contact->getLastname(),
                "role" => $contact->getRole(),
                "email" => $contact->getEmail(),
                "phoneCode" => $contact->getPhoneCode(),
                "phoneNum" => $contact->getPhoneNum()
            ]);
    
            $row = $sth->fetch();

            // if this contact exists, reutrn its id, else reutrn -1
            return $row ? $row[0] : -1;
        }
    
        /** Saves the contact if it doesn't exist yet, and returns its id */
        function save(Contact $contact) {
            global $pdo;

            $id = $this->getContactId($contact);

            // if the contact isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO contact VALUES (NULL, :firstname, :lastname, :role, :email, :phoneCode, :phoneNum)");
        
                $sth->execute([
                    "firstname" => $contact->getFirstname(),
                    "lastname" => $contact->getLastname(),
                    "role" => $contact->getRole(),
                    "email" => $contact->getEmail(),
                    "phoneCode" => $contact->getPhoneCode(),
                    "phoneNum" => $contact->getPhoneNum()
                ]);
                
                $id = $pdo->lastInsertId();
            }    

            return $id;
        }    

        function bind($contactId, $groupId) {
            global $pdo;

            $pdo->exec("INSERT INTO manage VALUES ($contactId, $groupId)");
        }
    }

