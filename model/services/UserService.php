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

        function findUserById($id) {
            global $pdo;

            $sql = "
                select firstname, lastname, email, phone, password
                from user
                where id = $id
            ";

            $sth = $pdo->query($sql);
            $userInfos = $sth->fetch(PDO::FETCH_NAMED);

            // if the user doesn't exist return null
            if(!$userInfos)
                return null;

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

        function isLocked(User $user) {
            global $pdo;

            $sth = $pdo->prepare("
                SELECT id
                from user
                where email = :email AND id IN (
                    SELECT user_id FROM locked_user
                )
            ");

            $sth->execute([
                "email" => $user->getEmail()
            ]);

            $locked = $sth->fetch();

            return !empty($locked);
        }

        /** Saves the user if it doesn't exist yet, and returns its id */
        function save(User $user) : int {
            global $pdo;
            
            $id = $this->getUserId($user);
            
            // if the user is already in the db, return its id
            if ($id != -1) 
                return $id;

            // else, add it to the db
            $sth = $pdo->prepare("INSERT INTO user VALUES (NULL, :firstname, :lastname, :email, :phone, :password)");

            $sth->execute([
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "email" => $user->getEmail(), 
                "phone" => $user->getPhone(),
                "password" => $user->getPassword()
            ]);

            $id = $pdo->lastInsertId();

            return $id;
        }

        function lockUser(User $user) {
            global $pdo;

            $id = $this->getUserId($user);

            // if the user doesn't exist, return
            if($id == -1)
                return;
            
            // lock the user
            if(!$this->isLocked($user)) {
                $token = bin2hex(random_bytes(64)); // the token lenght will be 128 (64*2, as hex = 4 bits and 1 byte = 8 bits)
                $sth = $pdo->prepare("INSERT INTO locked_user VALUES (:id, :token)");

                $sth->execute([
                    "id" => $id,
                    "token" => $token
                ]);

                return $token;
            }            
        }

        function unlockUser(User $user) {
            global $pdo;

            $id = $this->getUserId($user);

            // if the user doesn't exist, return
            if($id == -1)
                return;
            
            // unlock the user
            if($this->isLocked($user))
                $pdo->exec("DELETE FROM locked_user where user_id = $id");
        }

        function getLockedUserToken($user) {
            global $pdo;

            $id = $this->getUserId($user);

            // if the user doesn't exist, return
            if($id == -1)
                return;

            $token = $pdo->query("
                SELECT token
                FROM locked_user
                WHERE user_id = $id
            ")->fetch(PDO::FETCH_COLUMN, PDO::FETCH_NAMED);

            return $token;
        }
    }            