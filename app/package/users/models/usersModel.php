<?php

namespace CMW\Model\Users;

use CMW\Model\manager;

/**
 * Class: @usersModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class usersModel extends manager
{
    public int $userId;
    public string $userEmail;
    public ?string $userPseudo;
    public ?string $userFirstname;
    public ?string $userLastname;
    public int $userState;
    public string $userHighestRoleName;
    public string $userCreated;
    public string $userUpdated;
    public string $userLogged;
    private string $userPassword;
    private string $userKey;
    public array $userRoles;

    public function __construct($user_id = null)
    {
    }

    public static function getLoggedUser(): int
    {
        return $_SESSION['cmwUserId'] ?? -1;
    }

    public static function logIn($info, $cookie = false)
    {
        $password = $info["password"];
        $var = array(
            "user_email" => $info["email"]
        );
        $sql = "SELECT user_id, user_password FROM cmw_users WHERE user_state=1 AND user_email=:user_email";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $result = $req->fetch();
            if ($result) {
                if (password_verify($password, $result["user_password"])) {
                    $id = $result["user_id"];

                    $_SESSION['cmwUserId'] = $id;
                    if ($cookie) {
                        setcookie('cmw_cookies_user_id', $id, time() + 60 * 60 * 24 * 30, "/");
                    }

                    return $id;
                }

                return -1; // Password does not match
            }

            return -2; // Non-existent user
        }

        return -3; // SQL error
    }

    public static function logout(): void
    {
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        session_destroy();
    }

    public function create(array $roles): int
    {
        $var = array(
            'user_email' => $this->userEmail,
            'user_pseudo' => $this->userPseudo,
            'user_firstname' => $this->userFirstname,
            'user_lastname' => $this->userLastname,
            'user_state' => 1,
            'user_key' => uniqid('', true)
        );

        $sql = "INSERT INTO cmw_users (user_email, user_pseudo, user_firstname, user_lastname, user_state, user_key, user_created, user_updated) VALUES (:user_email, :user_pseudo, :user_firstname, :user_lastname, :user_state, :user_key, NOW(), NOW())";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $this->userId = $db->lastInsertId();
            $this->addRole($roles);
            return $this->userId;
        }

        return -1;
    }

    public function addRole(array $roles): void
    {
        foreach ($roles as $role) {

            $var = array(
                "user_id" => $this->userId,
                "role_id" => $role
            );

            $sql = "INSERT INTO cmw_users_roles (user_id, role_id) VALUES (:user_id, :role_id)";

            $db = manager::dbConnect();
            $req = $db->prepare($sql);
            $req->execute($var);
        }
    }

    public function fetch($user_id): void
    {
        $var = array(
            "user_id" => $user_id
        );

        $sql = "SELECT user_id, user_email, user_pseudo, user_firstname, user_lastname, user_state, DATE_FORMAT(user_created, '%d/%m/%Y à %H:%i:%s')
                AS 'user_created', DATE_FORMAT(user_updated, '%d/%m/%Y à %H:%i:%s')
                AS 'user_updated', DATE_FORMAT(user_logged, '%d/%m/%Y à %H:%i:%s')
                AS 'user_logged'
                FROM cmw_users WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $result = $req->fetch();

            foreach ($result as $key => $property) {

                //to camel case all keys (role_id => roleId (for $this->>roleId))
                $key = explode('_', $key);
                $firstElement = array_shift($key);
                $key = array_map('ucfirst', $key);
                array_unshift($key, $firstElement);
                $key = implode('', $key);

                if (property_exists(usersModel::class, $key)) {
                    $this->$key = $property;
                }
            }
        }
    }

    public function fetchAll(): array
    {
        $sql = "SELECT user_id, user_email, user_pseudo, user_firstname, user_lastname, user_state, DATE_FORMAT(user_created, '%d/%m/%Y à %H:%i:%s') AS 'user_created', DATE_FORMAT(user_updated, '%d/%m/%Y à %H:%i:%s') AS 'user_updated', DATE_FORMAT(user_logged, '%d/%m/%Y à %H:%i:%s') AS 'user_logged' FROM cmw_users";
        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute()) {
            return $req->fetchAll();
        }
        return [];
    }

    public function update(array $roles): void
    {
        $var = array(
            "user_id" => $this->userId,
            "user_email" => $this->userEmail,
            "user_pseudo" => mb_strimwidth($this->userPseudo, 0, 255),
            "user_firstname" => mb_strimwidth($this->userFirstname, 0, 255),
            "user_lastname" => mb_strimwidth($this->userLastname, 0, 255)
        );

        $sql = "UPDATE cmw_users SET user_email=:user_email,user_pseudo=:user_pseudo,user_firstname=:user_firstname,user_lastname=:user_lastname WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime();
        $this->updateRoles($roles);
    }

    public function updateEditTime(): void
    {
        $var = array(
            "user_id" => $this->userId,
        );

        $sql = "UPDATE cmw_users SET user_updated=CURRENT_TIMESTAMP WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateRoles(array $roles): void
    {
        //Delete all the roles of the players
        $var = array(
            "user_id" => $this->userId
        );

        $sql = "DELETE FROM cmw_users_roles WHERE user_id = :user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        //Add all the new roles
        $this->addRole($roles);
    }

    public function setPassword($password): void
    {
        $this->userPassword = $password;
    }

    public function updatePass(): void
    {
        $var = array(
            "user_id" => $this->userId,
            "user_password" => $this->userPassword
        );

        $sql = "UPDATE cmw_users SET user_password=:user_password WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime();
    }

    public function changeState(): void
    {
        $var = array(
            "user_id" => $this->userId,
            "user_state" => $this->userState,
        );

        $sql = "UPDATE cmw_users SET user_state=:user_state WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime();
    }

    public function delete(): void
    {
        $var = array(
            "user_id" => $this->userId,
        );
        $sql = "DELETE FROM cmw_users WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateLoggedTime(): void
    {
        $var = array(
            "user_id" => $this->userId,
        );

        $sql = "UPDATE cmw_users SET user_logged=CURRENT_TIMESTAMP WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function hasPermission(int $userId, string $permCode): int
    {
        $var = array(
            "user_id" => $userId,
            "perm_code" => $permCode
        );

        $sql = "SELECT cmw_permissions.permission_code FROM cmw_permissions
                    JOIN cmw_roles ON cmw_permissions.role_id = cmw_roles.role_id
                    JOIN cmw_users_roles on cmw_roles.role_id = cmw_users_roles.role_id
                    WHERE cmw_users_roles.user_id = :user_id AND cmw_permissions.permission_code = :perm_code";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return -1;
    }

    public static function getPlayerRoles(int $userId): array
    {
        $sql = "SELECT cmw_roles.role_name FROM cmw_users_roles
                    JOIN cmw_users on cmw_users.user_id = cmw_users_roles.user_id
                    JOIN cmw_roles on cmw_users_roles.role_id = cmw_roles.role_id
                    WHERE cmw_users.user_id = :user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute(array("user_id" => $userId))) {
            return $req->fetchAll();
        }

        return [];
    }

    public function checkMinecraftPseudo($pseudo): int
    {
        $req = file_get_contents("https://api.mojang.com/users/profiles/minecraft/$pseudo");

        return (int)($req === "NULL");
    }


    public function checkPseudo($pseudo): int
    {
        $var = array(
            "pseudo" => $pseudo
        );

        $sql = "SELECT * FROM `cmw_users` WHERE user_pseudo = :pseudo";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll());
        }

        return 0;
    }

    public function checkEmail($email): int
    {
        $var = array(
            "email" => $email
        );

        $sql = "SELECT * FROM `cmw_users` WHERE user_email = :email";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll());
        }

        return 0;
    }

}
