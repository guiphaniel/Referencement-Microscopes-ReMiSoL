<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/Contact.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class ContactService extends AbstractService {
        static private $instance;

        static function getInstance() : ContactService {
            if(!isset(self::$instance))
            self::$instance = new ContactService();
           
            return self::$instance;
        }

        function getContactId(Contact $contact) {
            global $pdo;
    
            $sth = $pdo->prepare("SELECT id FROM contact where firstname = :firstname and lastname = :lastname and email = :email and phone_code = :phoneCode and phone_num = :phoneNum and role = :role");
    
            $sth->execute([
                "firstname" => $contact->getFirstname(),
                "lastname" => $contact->getLastname(),
                "email" => $contact->getEmail(),
                "phoneCode" => $contact->getPhoneCode(),
                "phoneNum" => $contact->getPhoneNum(),
                "role" => $contact->getRole()
            ]);
    
            $row = $sth->fetch();

            // if this contact exists, return its id, else return -1
            $id = -1;
            if($row) {
                $id = $row[0];
                $contact->setId($id);
            }
            return $id;
        }
    
        /** Saves the contact if it doesn't exist yet, and returns its id */
        function save(Contact $contact) {
            global $pdo;

            $id = $this->getContactId($contact);

            // if the contact isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO contact VALUES (NULL, :firstname, :lastname, :email, :phoneCode, :phoneNum, :role)");
        
                $sth->execute([
                    "firstname" => $contact->getFirstname(),
                    "lastname" => $contact->getLastname(),
                    "email" => $contact->getEmail(),
                    "phoneCode" => $contact->getPhoneCode(),
                    "phoneNum" => $contact->getPhoneNum(),
                    "role" => $contact->getRole()
                ]);
                
                $id = $pdo->lastInsertId();
                $contact->setId($id);
            }    

            $contact->setId($id);

            return $id;
        }    

        function bind($contactId, $groupId) {
            global $pdo;

            $pdo->exec("INSERT INTO manage VALUES ($contactId, $groupId)");
        }

        function unbind($contactId, $groupId) {
            global $pdo;

            $pdo->exec("DELETE FROM manage WHERE contact_id = $contactId and microscopes_group_id = $groupId");
        }
    }

