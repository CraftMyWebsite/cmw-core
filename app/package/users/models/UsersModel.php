<?php

namespace CMW\Model\Users;

use CMW\Controller\Core\CoreController;
use CMW\Controller\Core\MailController;
use CMW\Controller\Users\UsersController;
use CMW\Entity\Users\RoleEntity;
use CMW\Entity\Users\UserPictureEntity;
use CMW\Entity\Users\UserEntity;

use CMW\Manager\Database\DatabaseManager;

use CMW\Manager\Lang\LangManager;
use CMW\Model\Core\CoreModel;
use CMW\Model\Core\MailModel;
use CMW\Utils\Utils;
use Exception;

/**
 * Class: @usersModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersModel extends DatabaseManager
{
    public function getUserById(int $id): ?UserEntity
    {

        $sql = "SELECT * FROM cmw_users WHERE user_id = :user_id";

        $db = self::getInstance();

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

        $userPictureSql = "SELECT * FROM cmw_users_pictures WHERE users_pictures_user_id = :user_id";

        $resUserPicture = $db->prepare($userPictureSql);

        $resUserPicture->execute(array("user_id" => $id));

        $resUserPicture = $resUserPicture->fetch();


        $userPicture = new UserPictureEntity(
            $resUserPicture['users_pictures_user_id'] ?? $id,
                $resUserPicture['users_pictures_image_name'] ?? ("default/" . UsersSettingsModel::getSetting("defaultImage")),
                $resUserPicture['users_pictures_last_update'] ?? null
            );

        $highestRole = $this->getUserHighestRole($res['user_id']);


        return new UserEntity(
            $res["user_id"],
            $res["user_email"],
            $res["user_pseudo"],
            $res["user_firstname"] ?? "",
            $res["user_lastname"] ?? "",
            $res["user_state"],
            $res["user_key"],
            $res["user_logged"],
            $roles,
            $highestRole,
            $res["user_created"],
            $res["user_updated"],
            $userPicture
        );
    }

    /**
     * @param string $pseudo
     * @return \CMW\Entity\Users\UserEntity|null
     */
    public function getUserWithPseudo(string $pseudo): ?UserEntity
    {
        $sql = "SELECT user_id FROM cmw_users WHERE user_pseudo = :pseudo";

        $db = self::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['pseudo' => $pseudo])){
            return null;
        }

        $res = $req->fetch();

        if (!$res){
            return null;
        }

        $userId = (int)$res['user_id'];

        return $this->getUserById($userId);
    }

    public static function getCurrentUser(): ?UserEntity
    {
        return !isset($_SESSION['cmwUserId']) ? null : (new self)->getUserById($_SESSION['cmwUserId']);
    }

    public function countUsers(): int
    {
        $sql = "select count('users_id') as `result` from cmw_users";
        $db = self::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute()) {
            return $res->fetch()['result'];
        }

        return 0;
    }


    public function getUsers(): array
    {
        $sql = "select user_id from cmw_users";
        $db = self::getInstance();

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
        return (int)($_SESSION['cmwUserId'] ?? -1);
    }

    public static function logIn($info, $cookie = false)
    {
        $password = $info["password"];
        $var = array(
            "user_email" => $info["email"]
        );
        $sql = "SELECT user_id, user_password FROM cmw_users WHERE user_state=1 AND user_email=:user_email";

        $db = self::getInstance();
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

    public function create(string $mail, ?string $username, ?string $firstName, ?string $lastName, array $roles): ?UserEntity
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

        $db = self::getInstance();
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

            $db = self::getInstance();
            $req = $db->prepare($sql);
            $req->execute($var);
        }
    }

    public function update(int $id, string $mail, ?string $username, ?string $firstname, ?string $lastname, array $roles): ?UserEntity
    {
        $var = array(
            "user_id" => $id,
            "user_email" => $mail,
            "user_pseudo" => $username !== null ? mb_strimwidth($username, 0, 255) : "",
            "user_firstname" => $firstname !== null ? mb_strimwidth($firstname, 0, 255) : "",
            "user_lastname" => $lastname !== null ? mb_strimwidth($lastname, 0, 255) : ""
        );

        $sql = "UPDATE cmw_users SET user_email=:user_email,user_pseudo=:user_pseudo,user_firstname=:user_firstname,user_lastname=:user_lastname WHERE user_id=:user_id";

        $db = self::getInstance();
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

        $db = self::getInstance();
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

        $db = self::getInstance();
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

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime($id);
    }

    public function updatePassWithMail(string $mail, string $password): void
    {
        $var = array(
            "user_email" => $mail,
            "user_password" => $password
        );

        $sql = "UPDATE cmw_users SET user_password=:user_password WHERE user_email=:user_email";

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateEditTime($db->lastInsertId());
    }

    public function changeState(int $id, int $state): void
    {
        $var = array(
            "user_id" => $id,
            "user_state" => $state,
        );

        $sql = "UPDATE cmw_users SET user_state=:user_state WHERE user_id=:user_id";

        $db = self::getInstance();
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

        $db = self::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    public function updateLoggedTime(int $id): void
    {
        $var = array(
            "user_id" => $id,
        );

        $sql = "UPDATE cmw_users SET user_logged=NOW() WHERE user_id=:user_id";

        $db = self::getInstance();
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
     * @return \CMW\Entity\Users\PermissionEntity[]
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
     * @return \CMW\Entity\Users\RoleEntity[]
     */
    public static function getRoles(int $userId): array
    {
        $rolesModel = new RolesModel();

        $sql = "SELECT role_id FROM cmw_users_roles WHERE user_id = :user_id";

        $db = self::getInstance();
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


    /**
     * @param int $userId
     * @return \CMW\Entity\Users\RoleEntity|null
     */
    public function getUserHighestRole(int $userId): ?RoleEntity
    {
        $rolesModel = new RolesModel();

        $sql = "SELECT cmw_users_roles.role_id 
                FROM cmw_users_roles
                JOIN cmw_roles ON cmw_users_roles.role_id = cmw_roles.role_id
                WHERE user_id = :user_id
                ORDER BY cmw_roles.role_weight DESC
                LIMIT 1";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(array("user_id" => $userId))) {
            return null;
        }

        $res = $req->fetch();

        if(empty($res)){
            return null;
        }

        return $rolesModel->getRoleById($res["role_id"]);
    }


    public function checkPseudo($pseudo): int
    {
        $var = array(
            "pseudo" => $pseudo
        );

        $sql = "SELECT user_id FROM `cmw_users` WHERE user_pseudo = :pseudo";

        $db = self::getInstance();
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

        $sql = "SELECT user_id FROM `cmw_users` WHERE user_email = :email";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll());
        }

        return 0;
    }

    public function isEmailAvailable(int $userId, string $email): bool
    {
        $var = array(
            "userId" => $userId,
            "email" => $email
        );

        $sql = "SELECT user_id FROM `cmw_users` WHERE user_email = :email AND user_id != :userId";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll()) <= 0;
        }

        return false;
    }

    public function isPseudoAvailable(int $userId, string $pseudo): bool
    {
        $var = array(
            "userId" => $userId,
            "pseudo" => $pseudo
        );

        $sql = "SELECT user_id FROM `cmw_users` WHERE user_pseudo = :pseudo AND user_id != :userId";

        $db = self::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll()) <= 0;
        }

        return false;
    }

    public function resetPassword(string $email): void
    {
       if (UsersSettingsModel::getSetting("resetPasswordMethod") === "0"){
            $this->resetPasswordMethodPasswordSendByMail($email);
       } elseif (UsersSettingsModel::getSetting("resetPasswordMethod") === "1"){
            $this->resetPasswordMethodUniqueLinkSendByMail($email);
       }
    }

    public function resetPasswordMethodPasswordSendByMail(string $email): void
    {
        $newPassword = $this->generatePassword();

        $this->updatePassWithMail($email, password_hash($newPassword, PASSWORD_BCRYPT));

        $this->sendResetPassword($email, $newPassword);
    }

    public function resetPasswordMethodUniqueLinkSendByMail(string $email): void
    {
        //TODO
    }

    public function sendResetPassword(string $email, string $password): void
    {
        $mailController = new MailController();
        $mailController->sendMail($email, LangManager::translate("users.login.forgot_password.mail.object",
            ["site_name" => (new CoreModel())->fetchOption("name")]),
            LangManager::translate("users.login.forgot_password.mail.body",
                ["password" => $password]));

    }

    private function generatePassword(): string
    {
        try {
            return bin2hex(Utils::genId(random_int(7, 12)));
        } catch (Exception $e) {
            return bin2hex(Utils::genId(10));
        }

    }

    //TODO set that in other class (try on installation to generate Controller for games ?)
    private function checkMinecraftPseudo($pseudo): int
    {
        $req = file_get_contents("https://api.mojang.com/users/profiles/minecraft/$pseudo");

        return (int)($req === "NULL");
    }

}
