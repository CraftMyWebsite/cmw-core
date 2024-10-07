<?php

namespace CMW\Implementation\Users\Users;

use CMW\Controller\Users\RolesController;
use CMW\Controller\Users\UsersLoginController;
use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Security\EncryptManager;
use CMW\Manager\Uploads\ImagesManager;
use CMW\Model\Users\UserPictureModel;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersOAuthModel;
use CMW\Type\Users\OAuthLoginStatus;
use CMW\Utils\Utils;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function explode;
use function is_null;

class UsersOAuthDiscordImplementation implements IUsersOAuth
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $redirectUri;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->clientId = $env->getValue('OAUTH_DISCORD_CLIENT_ID');
        $this->clientSecret = $env->getValue('OAUTH_DISCORD_CLIENT_SECRET');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/discord/connect';
    }

    public function methodeName(): string
    {
        return "Discord";
    }

    public function methodIdentifier(): string
    {
        return "discord";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/discord.png';
    }

    /**
     * @throws \JsonException
     */
    public function register(): OAuthLoginStatus
    {
        if (!isset($_GET['code'])) {
            return OAuthLoginStatus::INVALID_CODE;
        }

        $code = $_GET['code'];

        $token = $this->getAccessToken($code);

        if (!$token) {
            return OAuthLoginStatus::INVALID_TOKEN;
        }

        $userInfo = $this->getUserInfo($token);

        if (!$userInfo) {
            return OAuthLoginStatus::INVALID_USER_INFO;
        }

        $mail = $userInfo['email'];
        $encryptedMail = EncryptManager::encrypt($mail);

        $id = $userInfo['id'];

        //If account already exist with the same method, we logged the user...
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (!is_null($user)) {
            UsersLoginController::getInstance()->loginUser($user, true);
            return OAuthLoginStatus::SUCCESS_LOGIN;
        }

        if (UsersModel::getInstance()->checkEmail($encryptedMail)) {
            return OAuthLoginStatus::EMAIL_ALREADY_EXIST;
        }

        //If pseudo already exist, we add a random id after the pseudo
        if (UsersModel::getInstance()->checkPseudo($userInfo['username'])) {
            $userInfo['username'] .= "_" . Utils::genId(4);
        }

        $user = UsersModel::getInstance()->create(
            $encryptedMail,
            $userInfo['username'],
            $userInfo['given_name'] ?? null,
            $userInfo['family_name'] ?? null,
            RolesController::getInstance()->getDefaultRolesId(),
        );

        if (!$user) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_USER;
        }

        if (!UsersOAuthModel::getInstance()->createUser($user->getId(), $id, $this->methodIdentifier())) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_OAUTH_USER;
        }

        $userId = $userInfo['id'];
        $avatarHash = $userInfo['avatar'];

        if (!is_null($avatarHash)) {
            $imageLink = "https://cdn.discordapp.com/avatars/$userId/$avatarHash.png";
        } else {
            $discriminator = $userInfo['discriminator'] % 5; // Discord a 5 avatars par défaut
            $imageLink = "https://cdn.discordapp.com/embed/avatars/$discriminator.png";
        }

        try {
            $imageName = ImagesManager::downloadFromLink($imageLink, 'Users');
            UserPictureModel::getInstance()->uploadImage($user->getId(), $imageName);
        } catch (Exception $e) {
            // Gestion de l'erreur
        }


        //Login user
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_REGISTER;
    }

    /**
     * @throws \JsonException
     */
    public function login(): OAuthLoginStatus
    {
        if (!isset($_GET['code'])) {
            return OAuthLoginStatus::INVALID_CODE;
        }

        $code = $_GET['code'];

        $token = $this->getAccessToken($code);

        if (!$token) {
            return OAuthLoginStatus::INVALID_TOKEN;
        }

        $userInfo = $this->getUserInfo($token);

        if (!$userInfo) {
            return OAuthLoginStatus::INVALID_USER_INFO;
        }

        $id = $userInfo['id'];
        $mail = $userInfo['email'];
        $encryptedMail = EncryptManager::encrypt($mail);

        //If account doesn't exist, we register the user...
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (is_null($user)) {
            return $this->register();
        }

        //Login user
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_LOGIN;
    }

    /**
     * @throws \JsonException
     */
    private function getAccessToken(string $code): array
    {
        $url = 'https://discord.com/api/oauth2/token';

        $data = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            return [];
        }

        return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws \JsonException
     */
    private function getUserInfo($token)
    {
        $url = 'https://discord.com/api/users/@me';

        $options = [
            'http' => [
                'header' => "Authorization: Bearer " . $token['access_token'],
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            return null;
        }

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    #[NoReturn] public function redirectToConsent(): void
    {
        $authUrl = 'https://discord.com/api/oauth2/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => 'identify email',
                'prompt' => 'consent',
            ]);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
    <label for="oauth-discord-client-id">Discord Client ID</label>
    <div class="input-group">
        <i class="fa-solid fa-id-card"></i>
        <input type="text" id="oauth-discord-client-id" 
        name="oauth-discord-client-id" 
        value="$this->clientId"
        placeholder="client_id">
    </div>
    
    <label for="oauth-discord-client-secret">Discord Client Secret</label>
    <div class="input-group">
        <i class="fa-solid fa-lock"></i>
        <input type="text" id="oauth-discord-client-secret" 
        name="oauth-discord-client-secret" 
        value="$this->clientSecret"
        placeholder="client_secret">
    </div>
    
    <small>Plus d'informations sur notre <a href="#" target="_blank">WIKI</a></small>
HTML;

    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-discord-client-id'], $_POST['oauth-discord-client-secret'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'OAUTH_DISCORD_CLIENT_ID',
            FilterManager::filterInputStringPost('oauth-discord-client-id'),
        );
        $env->setOrEditValue(
            'OAUTH_DISCORD_CLIENT_SECRET',
            FilterManager::filterInputStringPost('oauth-discord-client-secret'),
        );
    }
}
