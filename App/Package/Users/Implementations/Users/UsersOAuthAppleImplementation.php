<?php

namespace CMW\Implementation\Users\Users;

use CMW\Controller\Users\RolesController;
use CMW\Controller\Users\UsersLoginController;
use CMW\Interface\Users\IUsersOAuth;
use CMW\Manager\Env\EnvManager;
use CMW\Manager\Filter\FilterManager;
use CMW\Manager\Security\EncryptManager;
use CMW\Model\Users\UsersModel;
use CMW\Model\Users\UsersOAuthModel;
use CMW\Type\Users\OAuthLoginStatus;
use CMW\Utils\Utils;
use JetBrains\PhpStorm\NoReturn;
use function base64_encode;
use function explode;
use function is_null;
use function rtrim;

class UsersOAuthAppleImplementation implements IUsersOAuth
{
    private ?string $clientId;
    private ?string $teamId;
    private ?string $keyId;
    private ?string $redirectUri;
    private ?string $privateKey;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->clientId = $env->getValue('OAUTH_APPLE_CLIENT_ID');
        $this->teamId = $env->getValue('OAUTH_APPLE_TEAM_ID');
        $this->keyId = $env->getValue('OAUTH_APPLE_KEY_ID');
        $this->privateKey = $env->getValue('OAUTH_APPLE_PRIVATE_KEY');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/apple/connect';
    }

    public function methodeName(): string
    {
        return "Apple";
    }

    public function methodIdentifier(): string
    {
        return "apple";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/apple.png';
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

        $mail = $userInfo['email'];
        $encryptedMail = EncryptManager::encrypt($mail);
        $id = $userInfo['sub'];

        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (!is_null($user)) {
            UsersLoginController::getInstance()->loginUser($user, true);
            return OAuthLoginStatus::SUCCESS_LOGIN;
        }

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

        $id = $userInfo['sub'];
        $mail = $userInfo['email'];
        $encryptedMail = EncryptManager::encrypt($mail);

        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (is_null($user)) {
            return $this->register();
        }

        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_LOGIN;
    }

    private function getAccessToken(string $code): ?string
    {
        $url = 'https://appleid.apple.com/auth/token';

        $data = [
            'client_id' => $this->clientId,
            'client_secret' => $this->generateClientSecret(),
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUri,
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
            return null;
        }

        $data = json_decode($result, true, 512, JSON_THROW_ON_ERROR);
        return $data['access_token'] ?? null;
    }

    private function generateClientSecret(): string
    {
        $header = json_encode(['alg' => 'ES256', 'kid' => $this->keyId], JSON_THROW_ON_ERROR);
        $claims = json_encode([
            'iss' => $this->teamId,
            'iat' => time(),
            'exp' => time() + 86400 * 180, // 180 jours
            'aud' => 'https://appleid.apple.com',
            'sub' => $this->clientId,
        ], JSON_THROW_ON_ERROR);

        $privateKey = str_replace(["\r", "\n"], '', $this->privateKey);
        $signature = '';
        openssl_sign($header . '.' . $claims, $signature, $privateKey, OPENSSL_ALGO_SHA256);

        return $this->base64url_encode($header) . '.' . $this->base64url_encode($claims) . '.' . $this->base64url_encode($signature);
    }

    private function getUserInfo($token)
    {
        $url = 'https://appleid.apple.com/auth/userinfo';

        $options = [
            'http' => [
                'header' => "Authorization: Bearer $token\r\n",
                'method' => 'GET',
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
        $authUrl = 'https://appleid.apple.com/auth/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => 'name email',
                'response_mode' => 'form_post',
            ]);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
            <label for="oauth-apple-client-id">Apple Client ID</label>
            <div class="input-group">
                <i class="fa-solid fa-id-card"></i>
                <input type="text" id="oauth-apple-client-id" 
                name="oauth-apple-client-id" 
                value="$this->clientId"
                placeholder="Client ID">
            </div>
            
            <label for="oauth-apple-key-id">Apple Key ID</label>
            <div class="input-group">
                <i class="fa-solid fa-key"></i>
                <input type="text" id="oauth-apple-key-id" 
                name="oauth-apple-key-id" 
                value="$this->keyId"
                placeholder="Key ID">
            </div>

            <label for="oauth-apple-team-id">Apple Team ID</label>
            <div class="input-group">
                <i class="fa-solid fa-users"></i>
                <input type="text" id="oauth-apple-team-id" 
                name="oauth-apple-team-id" 
                value="$this->teamId"
                placeholder="Team ID">
            </div>

            <label for="oauth-apple-private-key">Apple Private Key</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="text" id="oauth-apple-private-key" 
                name="oauth-apple-private-key" 
                value="$this->privateKey"
                placeholder="Private Key">
            </div>

            <small>Plus d'informations sur notre <a href="#" target="_blank">WIKI</a></small>
HTML;
    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-apple-client-id'], $_POST['oauth-apple-key-id'], $_POST['oauth-apple-team-id'], $_POST['oauth-apple-private-key'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'OAUTH_APPLE_CLIENT_ID',
            FilterManager::filterInputStringPost('oauth-apple-client-id'),
        );
        $env->setOrEditValue(
            'OAUTH_APPLE_KEY_ID',
            FilterManager::filterInputStringPost('oauth-apple-key-id'),
        );
        $env->setOrEditValue(
            'OAUTH_APPLE_TEAM_ID',
            FilterManager::filterInputStringPost('oauth-apple-team-id'),
        );
        $env->setOrEditValue(
            'OAUTH_APPLE_PRIVATE_KEY',
            FilterManager::filterInputStringPost('oauth-apple-private-key'),
        );
    }

    private function base64url_encode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}

