<?php
    include_once(__DIR__ . "/../start_db.php");
    include_once(__DIR__ . "/../entities/User.php");

    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
    });

    class UserService extends AbstractService {
        static private $instance;

        static function getInstance() : UserService{
            if(!isset(self::$instance))
                self::$instance = new UserService();
           
            return self::$instance;
        }

        function getUserId(User $user) {
            global $pdo;

            // email and phone are unique
            $sth = $pdo->prepare("SELECT id FROM user where email = :email or (phone_code = :phoneCode and phone_num = :phoneNum)");

            $sth->execute([
                "email" => $user->getEmail(),
                "phoneCode" => $user->getPhoneCode(),
                "phoneNum" => $user->getPhoneNum()
            ]);

            $row = $sth->fetch();

            // if this user exists, return its id, else return -1
            return $row ? $row[0] : -1;
        }

        function findAllUsers(int $limit = -1, int $offset = -1) {
            global $pdo;

            $sql = "
                select id, firstname, lastname, email, phone_code, phone_num, password
                from user
                ORDER BY firstname, lastname, phone_code, phone_num
            ";

            if($limit >=0) 
                $sql .= " LIMIT $limit";
            if($offset >=0) 
                $sql .= " OFFSET $offset";

            $users = [];
            foreach($pdo->query($sql, PDO::FETCH_NAMED) as $userInfos) {
                $user = new User($userInfos["firstname"], $userInfos["lastname"], $userInfos["email"], $userInfos["phone_code"], $userInfos["phone_num"], $userInfos["password"]);
                $user
                    ->setId($userInfos["id"])
                    ->setLocked($this->isLocked($user))
                    ->setAdmin($this->isAdmin($user));
                $users[] = $user;
            }

            return $users;
        }

        function countAllUsers() {
            global $pdo;

            $sql = "
                select count(id)
                from user
            ";

            return $pdo->query($sql)->fetch(PDO::FETCH_COLUMN);
        }

        function findAllAdmins() {
            global $pdo;

            $sql = "
                select id, firstname, lastname, email, phone_code, phone_num, password
                from user
                join admin as a
                on a.user_id = id
            ";

            $admins = [];
            foreach($pdo->query($sql, PDO::FETCH_NAMED) as $adminInfos) {
                $admin = new User($adminInfos["firstname"], $adminInfos["lastname"], $adminInfos["email"], $adminInfos["phone_code"], $adminInfos["phone_num"], $adminInfos["password"]);
                $admin->setId($adminInfos["id"])->setLocked($this->isLocked($admin))->setAdmin(true);
                $admins[] = $admin;
            }

            return $admins;
        }

        function findUserById($id) {
            global $pdo;

            $sql = "
                select id, firstname, lastname, email, phone_code, phone_num, password
                from user
                where id = $id
            ";

            $sth = $pdo->query($sql);
            $userInfos = $sth->fetch(PDO::FETCH_NAMED);

            // if the user doesn't exist return null
            if(!$userInfos)
                return null;

            $user = new User($userInfos["firstname"], $userInfos["lastname"], $userInfos["email"], $userInfos["phone_code"], $userInfos["phone_num"], $userInfos["password"]);
        
            return $user
                ->setId($userInfos["id"])
                ->setLocked($this->isLocked($user))
                ->setAdmin($this->isAdmin($user));
        }

        function findUserByEmail($email) {
            global $pdo;

            $sth = $pdo->prepare("
                select id, firstname, lastname, email, phone_code, phone_num, password
                from user
                where email = :email
            ");

            $sth->execute([
                "email" => $email
            ]);

            $userInfos = $sth->fetch(PDO::FETCH_NAMED);

            // if the user doesn't exist return null
            if(!$userInfos)
                return null;

            $user = new User($userInfos["firstname"], $userInfos["lastname"], $userInfos["email"], $userInfos["phone_code"], $userInfos["phone_num"], $userInfos["password"]);
            
            return $user->setId($userInfos["id"])->setLocked($this->isLocked($user))->setAdmin($this->isAdmin($user));
        }

        function findUserByPhone($phoneCode, $phoneNum) {
            global $pdo;

            $sth = $pdo->prepare("
                select id, firstname, lastname, email, phone_code, phone_num, password
                from user
                where phone_code = :phoneCode and phone_num = :phoneNum
            ");

            $sth->execute([
                "phoneCode" => $phoneCode, 
                "phoneNum" => $phoneNum
            ]);

            $userInfos = $sth->fetch(PDO::FETCH_NAMED);

            // if the user doesn't exist return null
            if(!$userInfos)
                return null;

            $user = new User($userInfos["firstname"], $userInfos["lastname"], $userInfos["email"], $userInfos["phone_code"], $userInfos["phone_num"], $userInfos["password"]);
            
            return $user->setId($userInfos["id"])->setLocked($this->isLocked($user))->setAdmin($this->isAdmin($user));
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

        function isAdmin($user) {
            global $pdo;

            if(is_int($user))
                $userId = $user;
            else
                $userId = $user->getId();

            $sth = $pdo->prepare("
                SELECT user_id 
                FROM admin
                WHERE user_id = :userId;
            ");

            $sth->execute([
                "userId" => $userId
            ]);

            $admin = $sth->fetch();

            return !empty($admin);
        }

        /** Saves the user if it doesn't exist yet, and returns its id */
        function save(User $user) : int {
            global $pdo;
            
            $id = $this->getUserId($user);
            
            // if the user is already in the db (i.e. has the same email or phone), throw
            if ($id != -1) 
                throw new Exception("Un compte existe déjà avec ces informations.");

            // else, add it to the db
            $sth = $pdo->prepare("INSERT INTO user VALUES (NULL, :firstname, :lastname, :normLastname, :email, :phoneCode, :phoneNum, :password)");

            $sth->execute([
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "normLastname" => $user->getNormLastname(),
                "email" => $user->getEmail(), 
                "phoneCode" => $user->getPhoneCode(),
                "phoneNum" => $user->getPhoneNum(),
                "password" => $user->getPassword()
            ]);

            $id = $pdo->lastInsertId();
            $user->setId($id);

            // if the user is admin, but not yet admin in db, add it as admin in db
            if($user->isAdmin() && !$this->isAdmin($user)) {
                $sth = $pdo->prepare("INSERT INTO admin VALUES (:id)");

                $sth->execute([
                    "id" => $id
                ]);
            }

            return $id;
        }

        function updateUser($user) {
            global $pdo;

            $id = $user->getId();
            $this->checkUserInfosUniqueness($user);

            $sth = $pdo->prepare("
                UPDATE user
                SET firstname = :firstname, lastname = :lastname, norm_lastname = :normLastname, email = :email, phone_code = :phoneCode, phone_num = :phoneNum, password = :password
                WHERE id = $id");

            $sth->execute([
                "firstname" => $user->getFirstname(),
                "lastname" => $user->getLastname(),
                "normLastname" => $user->getNormLastname(),
                "email" => $user->getEmail(), 
                "phoneCode" => $user->getPhoneCode(),
                "phoneNum" => $user->getPhoneNum(),
                "password" => $user->getPassword()
            ]);

            // lock / unlock the user
            if($user->isLocked())
                $this->lockUser($user);
            else if (!$user->isLocked() && $this->isLocked($user))
                $this->unlockUser($user);

            // if the user is admin, but not yet admin in db, add it as admin in db
            if($user->isAdmin() && !$this->isAdmin($user)) {
                $sth = $pdo->prepare("INSERT INTO admin VALUES (:id)");

                $sth->execute([
                    "id" => $id
                ]);
            } else if (!$user->isAdmin() && $this->isAdmin($user)) {
                if(sizeof($this->findAllAdmins()) <= 1)
                    throw new Exception("Il ne peut y avoir moins d'un administrateur.");
                $pdo->exec("DELETE FROM admin where user_id = $id");
            }
        }

        function checkUserInfosUniqueness($user) {
            $this->checkUserEmailUniqueness($user);
            $this->checkUserPhoneUniqueness($user);
        }

        function checkUserEmailUniqueness($user) {
            global $pdo;
            $id = $user->getId();
            
            $sth = $pdo->prepare("
                select id
                from user
                where id != $id and email = :email
            ");

            $sth->execute([
                "email" => $user->getEmail()
            ]);

            $row = $sth->fetch();

            if($row ? $row[0] : $id != $id) {
                if($_SESSION["user"]["admin"])
                    throw new Exception("Ce courriel est déjà pris par l'utilisateur " . $user->getFirstname() . " " . $user->getLastname() . ".");
                else
                    throw new Exception("Ce numéro de téléphone est déjà pris par un utilisateur.");
            }
        }

        function checkUserPhoneUniqueness(User $user) {
            global $pdo;
            $id = $user->getId();
            
            $sth = $pdo->prepare("
                select id
                from user
                where id != $id and phone_code = :phoneCode and phone_num = :phoneNum
            ");

            $sth->execute([
                "phoneCode" => $user->getPhoneCode(),
                "phoneNum" => $user->getPhoneNum()
            ]);

            $row = $sth->fetch();

            if($row ? $row[0] : $id != $id) {
                if($_SESSION["user"]["admin"])
                    throw new Exception("Ce numéro de téléphone est déjà pris par l'utilisateur " . $user->getFirstname() . " " . $user->getLastname() . ".");
                else
                    throw new Exception("Ce numéro de téléphone est déjà pris par un utilisateur.");
            }
        }

        function deleteUser($user) {
            global $pdo;

            if(is_int($user))
                $userId = $user;
            else
                $userId = $user->getId();

            $pdo->exec("DELETE FROM user where id = $userId");
        }

        function lockUser(User $user) {
            if($this->isLocked($user))
                return;

            global $pdo;

            $id = $this->getUserId($user);

            // if the user doesn't exist, return
            if($id == -1)
                return;
            
            // lock the user
            $token = bin2hex(random_bytes(64)); // the token lenght will be 128 (64*2, as hex = 4 bits and 1 byte = 8 bits)
            $sth = $pdo->prepare("INSERT INTO locked_user VALUES (:id, :token)");

            $sth->execute([
                "id" => $id,
                "token" => $token
            ]);

            $user->setLocked(true);

            return $token;
        }

        function unlockUser(User $user) {
            if(!$this->isLocked($user))
                return;

            global $pdo;

            $id = $this->getUserId($user);

            // if the user doesn't exist, return
            if($id == -1)
                return;
            
            // unlock the user
            $pdo->exec("DELETE FROM locked_user where user_id = $id");

            $user->setLocked(false);
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