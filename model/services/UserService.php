<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/User.php");

    class UserService {
        static private $instance;

        private function __construct() {}

        static function getInstance() {
            if(!isset(self::$instance))
                self::$instance = new UserService();
           
            return self::$instance;
        }

        function getUserId(User $user) {
            global $pdo;

            // email is unique
            $sth = $pdo->prepare("SELECT id FROM user where email = :email");

            $sth->execute([
                "email" => $user->getEmail()
            ]);

            $row = $sth->fetch();

            // if this user exists, reutrn its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findUserById($id) : User {
            global $pdo;

            $sql = "
                select firstname, lastname, email, phone, password
                from user
                where id = $id
            ";

            $sth = $pdo->query($sql);
            $userInfos = $sth->fetch(PDO::FETCH_NAMED);

            return new User($userInfos["firstname"], $userInfos["lastname"], $userInfos["email"], $userInfos["phone"], $userInfos["password"]);
        }

        function findUserByEmail($email) {
            global $pdo;

            $sth = $pdo->prepare("
                select firstname, lastname, email, phone, password
                from user
                where email = :email
            ");

            $sth->execute([
                "email" => $email
            ]);

            $userInfos = $sth->fetch(PDO::FETCH_NAMED);

            if($userInfos)
                return new User($userInfos["firstname"], $userInfos["lastname"], $userInfos["email"], $userInfos["phone"], $userInfos["password"]);
            else
                return null;
        }

        /** Saves the user if it doesn't exist yet, and returns its id */
        function save(User $user) : int {
            global $pdo;
            
            $id = $this->getUserId($user);
            
            // if the lab isn't already in the db, add it
            if ($id == -1)  {
                $sth = $pdo->prepare("INSERT INTO user VALUES (NULL, :firstname, :lastname, :email, :phone, :password)");

                $sth->execute([
                    "firstname" => $user->getFirstname(),
                    "lastname" => $user->getLastname(),
                    "email" => $user->getEmail(), 
                    "phone" => $user->getPhone(),
                    "password" => $user->getPassword()
                ]);

                $id = $pdo->lastInsertId();
            }          

            return $id;
        }
    }            