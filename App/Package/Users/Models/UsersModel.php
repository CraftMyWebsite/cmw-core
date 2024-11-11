<?php

namespace CMW\Model\Users;

use CMW\Controller\Users\UsersController;
use CMW\Controller\Users\UsersSessionsController;
use CMW\Entity\Users\RoleEntity;
use CMW\Entity\Users\User2FaEntity;
use CMW\Entity\Users\UserEntity;
use CMW\Manager\Database\DatabaseManager;
use CMW\Manager\Lang\LangManager;
use CMW\Manager\Mail\MailManager;
use CMW\Manager\Package\AbstractModel;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Twofa\TwoFaManager;
use CMW\Model\Core\CoreModel;
use CMW\Type\Users\LoginStatus;
use CMW\Utils\Utils;
use Exception;
use function count;

/**
 * Class: @usersModel
 * @package Users
 * @author CraftMyWebsite Team <contact@craftmywebsite.fr>
 * @version 1.0
 */
class UsersModel extends AbstractModel
{
    /**
     * @param int $id
     * @return UserEntity|null
     */
    public function getUserById(int $id): ?UserEntity
    {
        $sql = 'SELECT cmw_users.*, 2fa.users_2fa_is_enabled, users_2fa_secret,users_2fa_is_enforced  FROM cmw_users 
                JOIN cmw_users_2fa 2fa ON cmw_users.user_id = 2fa.users_2fa_user_id
                WHERE user_id = :user_id';

        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute(['user_id' => $id])) {
            return null;
        }

        $res = $res->fetch();

        if (!$res) {
            return null;
        }

        $roles = [];

        $roleSql = 'SELECT * FROM cmw_users_roles WHERE user_id = :user_id';
        $roleRes = $db->prepare($roleSql);

        if ($roleRes->execute(['user_id' => $id])) {
            $rolesModel = new RolesModel();

            $roleRes = $roleRes->fetchAll();

            foreach ($roleRes as $role) {
                $rlData = 'SELECT cmw_roles.*
                            FROM cmw_roles 
                            WHERE role_id = :role_id';
                $rlRes = $db->prepare($rlData);

                if (!$rlRes->execute(['role_id' => $role['role_id']])) {
                    continue;
                }

                $rl = $rlRes->fetch();

                if (!$rl) {
                    continue;
                }

                $roles[] = new RoleEntity(
                    $role['role_id'],
                    $rl['role_name'],
                    $rl['role_description'],
                    $rl['role_weight'],
                    $rl['role_is_default'],
                    $rolesModel->getPermissions($role['role_id'])
                );
            }
        }

        $highestRole = $this->getUserHighestRole($res['user_id']);

        return new UserEntity(
            $res['user_id'],
            EncryptManager::decrypt($res['user_email']),
            $res['user_pseudo'],
            $res['user_firstname'] ?? '',
            $res['user_lastname'] ?? '',
            $res['user_state'],
            $res['user_key'],
            new User2FaEntity(
                $res['user_id'],
                $res['users_2fa_is_enabled'],
                $res['users_2fa_secret'],
                $res['users_2fa_is_enforced']
            ),
            $res['user_logged'],
            $roles,
            $highestRole,
            $res['user_created'],
            $res['user_updated'],
            UsersController::getInstance()->getUserProfilePicture($res['user_id']),
            $this->getLoginMethode($res['user_id'])
        );
    }

    /**
     * @param string $pseudo
     * @return UserEntity|null
     */
    public function getUserWithPseudo(string $pseudo): ?UserEntity
    {
        $sql = 'SELECT user_id FROM cmw_users WHERE user_pseudo = :pseudo';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['pseudo' => $pseudo])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        $userId = (int)$res['user_id'];

        return $this->getUserById($userId);
    }

    /**
     * @param string $mail
     * @return UserEntity|null
     */
    public function getUserWithMail(string $mail): ?UserEntity
    {
        $sql = 'SELECT user_id FROM cmw_users WHERE user_email = :mail';

        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['mail' => $mail])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        $userId = (int)$res['user_id'];

        return $this->getUserById($userId);
    }

    /**
     * @return UserEntity|null
     * @deprecated Deprecated since version alpha-03
     * @see UsersSessionsController::getInstance()->getCurrentUser()
     */
    public static function getCurrentUser(): ?UserEntity
    {
        return UsersSessionsController::getInstance()->getCurrentUser();
    }

    /**
     * @return int
     */
    public function countUsers(): int
    {
        $sql = "SELECT COUNT('users_id') AS `result` FROM cmw_users";
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if ($res->execute()) {
            return $res->fetch()['result'];
        }

        return 0;
    }

    /**
     * @return UserEntity[]
     */
    public function getUsers(): array
    {
        $sql = 'SELECT user_id FROM cmw_users';
        $db = DatabaseManager::getInstance();

        $res = $db->prepare($sql);

        if (!$res->execute()) {
            return [];
        }

        $toReturn = [];

        while ($user = $res->fetch()) {
            Utils::addIfNotNull($toReturn, $this->getUserById($user['user_id']));
        }

        return $toReturn;
    }

    /**
     * @param string $mail
     * @param string $password
     * @return \CMW\Type\Users\LoginStatus|int
     * @des Return @userId if all is OK.
     */
    public function isCredentialsMatch(string $mail, string $password): LoginStatus|int
    {
        $sql = 'SELECT cmw_users.user_password, cmw_users.user_id FROM cmw_users WHERE user_state=1 AND user_email=:mail';
        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['mail' => $mail])) {
            return LoginStatus::INTERNAL_ERROR;
        }

        $res = $req->fetch();

        if (!$res) {
            return LoginStatus::NOT_FOUND;
        }

        return password_verify($password, $res['user_password']) ? $res['user_id'] : LoginStatus::NOT_MATCH;
    }

    /**
     * @param string $mail
     * @param string|null $username
     * @param string|null $firstName
     * @param string|null $lastName
     * @param array $roles
     * @return UserEntity|null
     */
    public function create(string $mail, ?string $username, ?string $firstName, ?string $lastName, array $roles): ?UserEntity
    {
        $var = [
            'user_email' => $mail,
            'user_pseudo' => $username,
            'user_firstname' => $firstName,
            'user_lastname' => $lastName,
            'user_state' => 1,
            'user_key' => uniqid('', true),
        ];

        $sql = 'INSERT INTO cmw_users (user_email, user_pseudo, user_firstname, user_lastname, user_state, user_key) 
                VALUES (:user_email, :user_pseudo, :user_firstname, :user_lastname, :user_state, :user_key)';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            $id = $db->lastInsertId();
            $this->addRoles($id, $roles);
            $tfaSecret = EncryptManager::encrypt((new TwoFaManager())->generateSecret());

            if (!Users2FaModel::getInstance()->create($id, $tfaSecret)) {
                return null;
            }

            return $this->getUserById($id);
        }

        return null;
    }

    /**
     * @param int $id
     * @param array $rolesId
     * @return void
     */
    public function addRoles(int $id, array $rolesId): void
    {
        foreach ($rolesId as $roleId) {
            $var = [
                'user_id' => $id,
                'role_id' => $roleId,
            ];

            $sql = 'INSERT INTO cmw_users_roles (user_id, role_id) VALUES (:user_id, :role_id)';

            $db = DatabaseManager::getInstance();
            $req = $db->prepare($sql);
            $req->execute($var);
        }
    }

    /**
     * @param int $id
     * @param string $mail
     * @param string|null $username
     * @param string|null $firstname
     * @param string|null $lastname
     * @param array $roles
     * @return UserEntity|null
     */
    public function update(int $id, string $mail, ?string $username, ?string $firstname, ?string $lastname, array $roles): ?UserEntity
    {
        $var = [
            'user_id' => $id,
            'user_email' => $mail,
            'user_pseudo' => $username,
            'user_firstname' => $firstname,
            'user_lastname' => $lastname,
        ];

        $sql = 'UPDATE cmw_users SET user_email=:user_email,user_pseudo=:user_pseudo,user_firstname=:user_firstname,user_lastname=:user_lastname WHERE user_id=:user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);

        $this->updateRoles($id, $roles);

        return $this->getUserById($id);
    }

    /**
     * @param int $id
     * @param array $roles
     * @return void
     */
    private function updateRoles(int $id, array $roles): void
    {
        // Delete all the roles of the players
        $var = [
            'user_id' => $id,
        ];

        $sql = 'DELETE FROM cmw_users_roles WHERE user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);

        // Add all the new roles
        $this->addRoles($id, $roles);
    }

    /**
     * @param int $id
     * @param string $password
     * @return bool
     */
    public function updatePass(int $id, string $password): bool
    {
        $var = [
            'user_id' => $id,
            'user_password' => $password,
        ];

        $sql = 'UPDATE cmw_users SET user_password=:user_password WHERE user_id=:user_id';

        $db = DatabaseManager::getInstance();
        return $db->prepare($sql)->execute($var);
    }

    /**
     * @param string $mail
     * @param string $password
     * @return void
     */
    public function updatePassWithMail(string $mail, string $password): void
    {
        $var = [
            'user_email' => $mail,
            'user_password' => $password,
        ];

        $sql = 'UPDATE cmw_users SET user_password=:user_password WHERE user_email=:user_email';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    /**
     * @param int $id
     * @param int $state
     * @return void
     */
    public function changeState(int $id, int $state): void
    {
        $var = [
            'user_id' => $id,
            'user_state' => $state,
        ];

        $sql = 'UPDATE cmw_users SET user_state=:user_state WHERE user_id=:user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    /**
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $var = [
            'user_id' => $id,
        ];
        $sql = 'DELETE FROM cmw_users WHERE user_id=:user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    /**
     * @param int $id
     * @return void
     */
    public function updateLoggedTime(int $id): void
    {
        $var = [
            'user_id' => $id,
        ];

        $sql = 'UPDATE cmw_users SET user_logged=NOW() WHERE user_id=:user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);
        $req->execute($var);
    }

    /**
     * @param UserEntity|null $user
     * @param string ...$permCode
     * @return bool
     */
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

        $toReturn = [];
        foreach ($roles as $role) {
            $permissions = RolesModel::getInstance()->getPermissions($role->getId());
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

        $sql = 'SELECT role_id FROM cmw_users_roles WHERE user_id = :user_id';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['user_id' => $userId])) {
            return [];
        }

        $toReturn = [];

        while ($role = $req->fetch()) {
            Utils::addIfNotNull($toReturn, $rolesModel->getRoleById($role['role_id']));
        }

        return $toReturn;
    }

    /**
     * @param int $userId
     * @return \CMW\Entity\Users\RoleEntity|null
     */
    public function getUserHighestRole(int $userId): ?RoleEntity
    {
        $sql = 'SELECT cmw_users_roles.role_id 
                FROM cmw_users_roles
                JOIN cmw_roles ON cmw_users_roles.role_id = cmw_roles.role_id
                WHERE user_id = :user_id
                ORDER BY cmw_roles.role_weight DESC
                LIMIT 1';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if (!$req->execute(['user_id' => $userId])) {
            return null;
        }

        $res = $req->fetch();

        if (empty($res)) {
            return null;
        }

        return RolesModel::getInstance()->getRoleById($res['role_id']);
    }

    /**
     * @param $pseudo
     * @return int
     */
    public function checkPseudo($pseudo): int
    {
        $var = [
            'pseudo' => $pseudo,
        ];

        $sql = 'SELECT user_id FROM `cmw_users` WHERE user_pseudo = :pseudo';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll());
        }

        return 0;
    }

    /**
     * @param $email
     * @return int
     */
    public function checkEmail($email): int
    {
        $var = [
            'email' => $email,
        ];

        $sql = 'SELECT user_id FROM `cmw_users` WHERE user_email = :email';

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute($var)) {
            return count($req->fetchAll());
        }

        return 0;
    }

    /**
     * @param string $email
     * @return void
     */
    public function resetPassword(string $email): void
    {
        if (UsersSettingsModel::getSetting('resetPasswordMethod') === '0') {
            $this->resetPasswordMethodPasswordSendByMail($email);
        } elseif (UsersSettingsModel::getSetting('resetPasswordMethod') === '1') {
            $this->resetPasswordMethodUniqueLinkSendByMail($email);
        }
    }

    /**
     * @param string $email
     * @return void
     */
    public function resetPasswordMethodPasswordSendByMail(string $email): void
    {
        $newPassword = $this->generatePassword();

        $this->updatePassWithMail($email, password_hash($newPassword, PASSWORD_BCRYPT));

        $this->sendResetPassword($email, $newPassword);
    }

    /**
     * @param string $email
     * @param string $password
     * @return void
     */
    public function sendResetPassword(string $email, string $password): void
    {
        MailManager::getInstance()->sendMail($email, LangManager::translate('users.login.forgot_password.mail.object',
            ['site_name' => (new CoreModel())->fetchOption('name')]),
            LangManager::translate('users.login.forgot_password.mail.body',
                ['password' => $password]));
    }

    /**
     * @return string
     * @desc Generate random password
     */
    private function generatePassword(): string
    {
        try {
            return bin2hex(Utils::genId(random_int(7, 12)));
        } catch (Exception) {
            return bin2hex(Utils::genId(10));
        }
    }

    /**
     * @param string $mail
     * @return bool
     */
    public function isEmailUseOAuth(string $mail): bool
    {
        $sql = "SELECT cmw_users.user_id 
                FROM `cmw_users` 
                JOIN `cmw_users_oauth` 
                ON cmw_users_oauth.user_id = cmw_users.user_id
                WHERE cmw_users.user_email = :mail";

        $db = DatabaseManager::getInstance();
        $req = $db->prepare($sql);

        if ($req->execute(["mail" => $mail])) {
            return $req->columnCount() > 0;
        }

        return false;
    }

    /**
     * @param int $userId
     * @return string|null
     * @desc Return the implementation identifier. If NULL, the user is not an OAuth user.
     */
    public function getLoginMethode(int $userId): ?string
    {
        $sql = "SELECT methode FROM cmw_users_oauth WHERE user_id = :user_id";
        $db = DatabaseManager::getInstance();

        $req = $db->prepare($sql);

        if (!$req->execute(['user_id' => $userId])) {
            return null;
        }

        $res = $req->fetch();

        if (!$res) {
            return null;
        }

        return $res['methode'];
    }
}
