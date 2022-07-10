<?php

namespace CMW\Model\Users;

use CMW\Controller\Roles\RolesController;
use CMW\Entity\Roles\RoleEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Model\Manager;
use CMW\Model\Permissions\PermissionsModel;
use CMW\Model\Roles\RolesModel;
use CMW\Utils\Utils;

/**
 * Class: @usersModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersModel extends Manager
{
    public function getUserById(int $id): ?UserEntity
    {

        $sql = "select * from cmw_users where user_id = :user_id";

        $db = Manager::dbConnect();

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

            $rolesModel = new RolesModel();

            $roleRes = $roleRes->fetchAll();

            foreach ($roleRes as $role) {

                $rlData = "SELECT cmw_roles.*
                            FROM cmw_roles 
                            WHERE role_id = :role_id";
                $rlRes = $db->prepare($rlData);

                if (!$rlRes->execute(array("role_id" => $role["role_id"]))) {
                    continue;
                }

                $rl = $rlRes->fetch();

                if (!$rl) {
                    continue;
                }

                $roles[] = new RoleEntity(
                    $role["role_id"],
                    $rl["role_name"],
                    $rl["role_description"],
                    $rl["role_weight"],
                    $rolesModel->getPermissions($role["role_id"])
                );

            }

        }

        return new UserEntity(
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
        $db = Manager::dbConnect();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return array();
        }

        $toReturn = array();

        while ($user = $res->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getUserById($user["user_id"]));
        }

        return $toReturn;
    }

    public static function getLoggedUser(): int
    {
        return isset($_SESSION['cmwUserId']) ?? -1;
    }

    public static function logIn($info, $cookie = false)
    {
        $password = $info["password"];
        $var = array(
            "user_email" => $info["email"]
        );
        $sql = "SELECT user_id, user_password FROM cmw_users WHERE user_state=1 AND user_email=:user_email";

        $db = Manager::dbConnect();
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

    public function create(string $mail, string $username, string $firstName, string $lastName, array $roles): ?UserEntity
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

        $db = Manager::dbConnect();
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

            $db = Manager::dbConnect();
            $req = $db->prepare($sql);
            $req->execute($var);
        }
    }

    public function update(int $id, string $mail, string $username, string $firstname, string $lastname, array $roles): ?UserEntity
    {
        $var = array(
            "user_id" => $id,
            "user_email" => $mail,
            "user_pseudo" => mb_strimwidth($username, 0, 255),
            "user_firstname" => mb_strimwidth($firstname, 0, 255),
            "user_lastname" => mb_strimwidth($lastname, 0, 255)
        );

        $sql = "UPDATE cmw_users SET user_email=:user_email,user_pseudo=:user_pseudo,user_firstname=:user_firstname,user_lastname=:user_lastname WHERE user_id=:user_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime($id);
        $this->updateRoles($id, $roles);

        return $this->getUserById($id);
    }

    private function updateEditTime(int $id): void
    {
        $var = array(
            "user_id" => $id,
        );

        $sql = "UPDATE cmw_users SET user_updated=NOW() WHERE user_id=:user_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    private function updateRoles(int $id, array $roles): void
    {
        //Delete all the roles of the players
        $var = array(
            "user_id" => $id
        );

        $sql = "DELETE FROM cmw_users_roles WHERE user_id = :user_id";

        $db = Manager::dbConnect();
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

        $db = Manager::dbConnect();
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

        $db = Manager::dbConnect();
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

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateLoggedTime(int $id): void
    {
        $var = array(
            "user_id" => $id,
        );

        $sql = "UPDATE cmw_users SET user_logged=NOW() WHERE user_id=:user_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public static function hasPermission(?UserEntity $user, string ...$permCode): bool
    {
        if (is_null($user)) {
            return false;
        }

        foreach ($permCode as $perm) {
            if (!PermissionsModel::hasPermissions(self::getPermissions($user->getId()), $perm)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return \CMW\Entity\Permissions\PermissionEntity[]
     */
    public static function getPermissions(int $userId): array
    {

        $roles = self::getRoles($userId);

        $rolesModel = new RolesModel();

        $toReturn = array();
        foreach ($roles as $role) {

            $permissions = $rolesModel->getPermissions($role->getId());
            foreach ($permissions as $permission) {
                $toReturn[] = $permission;
            }

        }

        return $toReturn;

    }


    /**
     * @return \CMW\Entity\Roles\RoleEntity[]
     */
    public static function getRoles(int $userId): array
    {
        $rolesModel = new RolesModel();

        $sql = "SELECT role_id FROM cmw_users_roles WHERE user_id = :user_id";

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if (!$req->execute(array("user_id" => $userId))) {
            return array();
        }

        $toReturn = array();

        while ($role = $req->fetch()) {
            Utils::addIfNotNull($toReturn, $rolesModel->getRoleById($role["role_id"]));
        }

        return $toReturn;
    }


    public function checkPseudo($pseudo): int
    {
        $var = array(
            "pseudo" => $pseudo
        );

        $sql = "SELECT * FROM `cmw_users` WHERE user_pseudo = :pseudo";

        $db = Manager::dbConnect();
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

        $db = Manager::dbConnect();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll());
        }

        return 0;
    }

    //TODO set that in other class (try on installation to generate Controller for games ?)
    private function checkMinecraftPseudo($pseudo): int
    {
        $req = file_get_contents("https://api.mojang.com/users/profiles/minecraft/$pseudo");

        return (int)($req === "NULL");
    }

}
