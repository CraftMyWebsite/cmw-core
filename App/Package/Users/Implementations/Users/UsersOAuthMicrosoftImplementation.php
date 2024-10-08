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
use JsonException;

class UsersOAuthMicrosoftImplementation implements IUsersOAuth
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $redirectUri;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->clientId = $env->getValue('OAUTH_MICROSOFT_CLIENT_ID');
        $this->clientSecret = $env->getValue('OAUTH_MICROSOFT_CLIENT_SECRET');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/microsoft/connect';
    }

    public function methodeName(): string
    {
        return "Microsoft";
    }

    public function methodIdentifier(): string
    {
        return "microsoft";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/microsoft.png';
    }

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

        $mail = $userInfo['mail'] ?? $userInfo['userPrincipalName'];
        $encryptedMail = EncryptManager::encrypt($mail);

        $id = $userInfo['id'];

        // Check if the user already exists with this method
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (!is_null($user)) {
            UsersLoginController::getInstance()->loginUser($user, true);
            return OAuthLoginStatus::SUCCESS_LOGIN;
        }

        // If email already exists, we cannot register a new account
        if (UsersModel::getInstance()->checkEmail($encryptedMail)) {
            return OAuthLoginStatus::EMAIL_ALREADY_EXIST;
        }

        $pseudo = ucfirst(Utils::normalizeForSlug(explode('@', $mail)[0]));

        if (UsersModel::getInstance()->checkPseudo($pseudo)) {
            $pseudo .= Utils::generateRandomNumber(3);
        }

        $user = UsersModel::getInstance()->create(
            $encryptedMail,
            $pseudo,
            $userInfo['givenName'] ?? null,
            $userInfo['surname'] ?? null,
            RolesController::getInstance()->getDefaultRolesId(),
        );

        if (!$user) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_USER;
        }

        if (!UsersOAuthModel::getInstance()->createUser($user->getId(), $id, $this->methodIdentifier())) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_OAUTH_USER;
        }

        $imageLink = $userInfo['photo'] ?? null;

        if ($imageLink) {
            try {
                $imageName = ImagesManager::downloadFromLink($imageLink, 'Users');
                UserPictureModel::getInstance()->uploadImage($user->getId(), $imageName);
            } catch (Exception) {
            }
        }

        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_REGISTER;
    }

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
        $mail = $userInfo['mail'] ?? $userInfo['userPrincipalName'];
        $encryptedMail = EncryptManager::encrypt($mail);

        // If user doesn't exist, register a new account
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (is_null($user)) {
            return $this->register();
        }

        // Login user
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_LOGIN;
    }

    /**
     * Exchange code for access token
     * @throws JsonException
     */
    private function getAccessToken(string $code): array
    {
        $url = 'https://login.microsoftonline.com/common/oauth2/v2.0/token';

        $data = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'scope' => 'https://graph.microsoft.com/.default',
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
     * Fetch user information from Microsoft Graph API
     * @throws JsonException
     */
    private function getUserInfo(array $token): array
    {
        $url = 'https://graph.microsoft.com/v1.0/me';

        $options = [
            'http' => [
                'header' => "Authorization: Bearer " . $token['access_token'] . "\r\n",
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        if ($result === false) {
            return [];
        }

        return json_decode($result, true, 512, JSON_THROW_ON_ERROR);
    }

    #[NoReturn] public function redirectToConsent(): void
    {
        $authUrl = 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize?' . http_build_query([
                'client_id' => $this->clientId,
                'response_type' => 'code',
                'redirect_uri' => $this->redirectUri,
                'response_mode' => 'query',
                'scope' => 'https://graph.microsoft.com/User.Read openid email profile offline_access',
            ]);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
            <label for="oauth-microsoft-client-id">Microsoft Client ID</label>
            <div class="input-group">
                <i class="fa-solid fa-id-card"></i>
                <input type="text" id="oauth-microsoft-client-id" 
                name="oauth-microsoft-client-id" 
                value="$this->clientId"
                placeholder="client_id">
            </div>
            
            <label for="oauth-microsoft-client-secret">Microsoft Client Secret</label>
            <div class="input-group">
                <i class="fa-solid fa-key"></i>
                <input type="text" id="oauth-microsoft-client-secret" 
                name="oauth-microsoft-client-secret" 
                value="$this->clientSecret"
                placeholder="client_secret">
            </div>
        HTML;
    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-microsoft-client-id'], $_POST['oauth-microsoft-client-secret'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'OAUTH_MICROSOFT_CLIENT_ID',
            FilterManager::filterInputStringPost('oauth-microsoft-client-id'),
        );
        $env->setOrEditValue(
            'OAUTH_MICROSOFT_CLIENT_SECRET',
            FilterManager::filterInputStringPost('oauth-microsoft-client-secret'),
        );
    }
}