<?php

namespace CMW\Model\Users;

use CMW\Entity\Roles\roleEntity;
use CMW\Entity\Users\userEntity;
use CMW\Model\manager;

/**
 * Class: @usersModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class usersModel extends manager
{
    public function getUserById(int $id): ?userEntity
    {

        $sql = "select * from cmw_users where user_id = :user_id";

        $db = manager::dbConnect();

        $res = $db->prepare($sql);

        if (!$res->execute(array("user_id" => $id))) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }


        $roles = array();

        $roleSql = "select * from cmw_users_roles where user_id = :user_id";
        $roleRes = $db->prepare($roleSql);

        if ($roleRes->execute(array("user_id" => $id))) {

            $roleRes = $roleRes->fetchAll();

            foreach ($roleRes as $role) {

                $rlData = "SELECT cmw_roles.*, cmw_roles_permissions.* 
                            FROM cmw_roles 
                            JOIN cmw_roles_permissions 
                            ON cmw_roles.role_id = cmw_roles_permissions.role_permission_role_id 
                            WHERE role_id = :role_id";
                $rlRes = $db->prepare($rlData);

                if (!$rlRes->execute(array("role_id" => $role["role_id"]))) {
                    continue;
                }

                $rl = $rlRes->fetch();

                if (!$rl) {
                    continue;
                }

                $roles[] = new roleEntity(
                    $role["role_id"],
                    $rl["role_name"],
                    $rl["role_description"],
                    $rl["role_weight"],
                    $rl["role_permission_id"],
                    $rl["role_permission_code"],
                    $rl["role_permission_role_id"]
                );

            }

        }

        return new userEntity(
            $res["user_id"],
            $res["user_email"],
            $res["user_pseudo"],
            $res["user_firstname"] ?? "",
            $res["user_lastname"] ?? "",
            $res["user_state"],
            $res["user_logged"],
            $roles,
            $res["user_created"],
            $res["user_updated"],
        );
    }

    public function getUsers(): array
    {
        $sql = "select user_id from cmw_users";
        $db = manager::dbConnect();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($user = $res->fetch()) {
            $toReturn[] = $this->getUserById($user["user_id"]);
        }

        return $toReturn;
    }


    public static function getLoggedUser(): int
    {
        return isset($_SESSION['cmwUserId']) ?: -1;
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

    public static function logOut(): void
    {
        $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
        session_destroy();
    }

    public function createUser(string $mail, string $username, string $firstName, string $lastName, array $roles): ?userEntity
    {
        $var = array(
            'user_email' => $mail,
            'user_pseudo' => $username,
            'user_firstname' => $firstName,
            'user_lastname' => $lastName,
            'user_state' => 1,
            'user_key' => uniqid('', true)
        );

        $sql = "INSERT INTO cmw_users (user_email, user_pseudo, user_firstname, user_lastname, user_state, user_key) 
                VALUES (:user_email, :user_pseudo, :user_firstname, :user_lastname, :user_state, :user_key)";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            $this->addRole($id, $roles);
            return $this->getUserById($id);
        }

        return null;
    }

    public function addRole(int $id, array $rolesId): void
    {
        foreach ($rolesId as $roleId) {

            $var = array(
                "user_id" => $id,
                "role_id" => $roleId
            );

            $sql = "INSERT INTO cmw_users_roles (user_id, role_id) VALUES (:user_id, :role_id)";

            $db = manager::dbConnect();
            $req = $db->prepare($sql);
            $req->execute($var);
        }
    }

    public function updateUser(int $id, string $mail, string $username, string $firstname, string $lastname, array $roles): ?userEntity
    {
        $var = array(
            "user_id" => $id,
            "user_email" => $mail,
            "user_pseudo" => mb_strimwidth($username, 0, 255),
            "user_firstname" => mb_strimwidth($firstname, 0, 255),
            "user_lastname" => mb_strimwidth($lastname, 0, 255)
        );

        $sql = "UPDATE cmw_users SET user_email=:user_email,user_pseudo=:user_pseudo,user_firstname=:user_firstname,user_lastname=:user_lastname WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime($id);
        $this->updateRoles($id, $roles);

        return $this->getUserById($id);
    }

    public function updateEditTime(int $id): void
    {
        $var = array(
            "user_id" => $id,
        );

        $sql = "UPDATE cmw_users SET user_updated=NOW() WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateRoles(int $id, array $roles): void
    {
        //Delete all the roles of the players
        $var = array(
            "user_id" => $id
        );

        $sql = "DELETE FROM cmw_users_roles WHERE user_id = :user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        //Add all the new roles
        $this->addRole($id, $roles);
    }

    public function updatePass($id, $password): void
    {
        $var = array(
            "user_id" => $id,
            "user_password" => $password
        );

        $sql = "UPDATE cmw_users SET user_password=:user_password WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime($id);
    }

    public function changeState(int $id, int $state): void
    {
        $var = array(
            "user_id" => $id,
            "user_state" => $state,
        );

        $sql = "UPDATE cmw_users SET user_state=:user_state WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime($id);
    }

    public function delete(int $id): void
    {
        $var = array(
            "user_id" => $id,
        );
        $sql = "DELETE FROM cmw_users WHERE user_id=:user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateLoggedTime(int $id): void
    {
        $var = array(
            "user_id" => $id,
        );

        $sql = "UPDATE cmw_users SET user_logged=NOW() WHERE user_id=:user_id";

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

        $sql = "SELECT cmw_roles_permissions.role_permission_code FROM cmw_roles_permissions
                    JOIN cmw_roles ON cmw_roles_permissions.role_permission_role_id = cmw_roles.role_id
                    JOIN cmw_users_roles on cmw_roles.role_id = cmw_users_roles.role_id
                    WHERE cmw_users_roles.user_id = :user_id AND cmw_roles_permissions.role_permission_code = :perm_code";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $lines = $req->fetchAll();

            return count($lines);
        }

        return -1;
    }


    /**
     * @param int $userId
     * @return \CMW\Entity\Roles\roleEntity[]
     */
    public static function getUserRoles(int $userId): array
    {
        $sql = "SELECT cmw_roles.role_name, cmw_roles_permissions.* FROM cmw_users_roles
                    JOIN cmw_users ON cmw_users.user_id = cmw_users_roles.user_id
                    JOIN cmw_roles ON cmw_users_roles.role_id = cmw_roles.role_id   
                    JOIN cmw_roles_permissions ON cmw_roles.role_id = cmw_roles_permissions.role_permission_role_id                                
                    WHERE cmw_users.user_id = :user_id";

        $db = manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("user_id" => $userId))) {
            return array();
        }

        $res = $req->fetchAll();
        $toReturn = array();

        foreach ($res as $role) {
            $toReturn[] = new roleEntity(
                $role["role_id"],
                $role["role_name"],
                $role["role_description"],
                $role["role_weight"],
                $role["role_permission_id"],
                $role["role_permission_code"],
                $role["role_permission_role_id"]
            );
        }

        return $toReturn;
    }

    //TODO set that in other class (try on installation to generate Controller for games ?)
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
