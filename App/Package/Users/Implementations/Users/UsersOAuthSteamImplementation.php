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
use function is_null;

class UsersOAuthSteamImplementation implements IUsersOAuth
{
    private ?string $apiKey;
    private ?string $redirectUri;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->apiKey = $env->getValue('STEAM_API_KEY');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/steam/connect';
    }

    public function methodeName(): string
    {
        return "Steam";
    }

    public function methodIdentifier(): string
    {
        return "steam";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/steam.png';
    }

    public function register(): OAuthLoginStatus
    {

        if (!isset($_GET['openid_identity'])) {
            return OAuthLoginStatus::INVALID_CODE;
        }

        $steamId = $this->extractSteamId($_GET['openid_identity']);

        if (!$steamId) {
            return OAuthLoginStatus::INVALID_USER_INFO;
        }

        $userInfo = $this->getUserInfo($steamId);

        if (!$userInfo) {
            return OAuthLoginStatus::INVALID_USER_INFO;
        }

        //Steam does not provide email, so we generate one
        $mail = 'steam_' . $steamId . '@example.com';
        $encryptedMail = EncryptManager::encrypt($mail);

        $user = UsersOAuthModel::getInstance()->getUser($steamId, $encryptedMail, $this->methodIdentifier());

        if (!is_null($user)) {
            UsersLoginController::getInstance()->loginUser($user, true);
            return OAuthLoginStatus::SUCCESS_LOGIN;
        }

        if (UsersModel::getInstance()->checkEmail($encryptedMail)) {
            return OAuthLoginStatus::EMAIL_ALREADY_EXIST;
        }

        $pseudo = ucfirst(Utils::normalizeForSlug($userInfo['personaname']));

        //If the pseudo already exists, we add a random id
        if (UsersModel::getInstance()->checkPseudo($pseudo)) {
            $pseudo .= Utils::generateRandomNumber(3);
        }

        $user = UsersModel::getInstance()->create(
            $encryptedMail,
            $pseudo,
            null,
            null,
            RolesController::getInstance()->getDefaultRolesId(),
        );

        if (!$user) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_USER;
        }

        if (!UsersOAuthModel::getInstance()->createUser($user->getId(), $steamId, $this->methodIdentifier())) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_OAUTH_USER;
        }

        try {
            $imageName = ImagesManager::downloadFromLink($userInfo['avatarfull'], 'Users');
            UserPictureModel::getInstance()->uploadImage($user->getId(), $imageName);
        } catch (Exception) {
        }

        // Login user
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_REGISTER;
    }

    public function login(): OAuthLoginStatus
    {
        if (!isset($_GET['openid_identity'])) {
            return OAuthLoginStatus::INVALID_CODE;
        }

        $steamId = $this->extractSteamId($_GET['openid_identity']);

        if (!$steamId) {
            return OAuthLoginStatus::INVALID_USER_INFO;
        }

        //Steam does not provide email, so we generate one
        $mail = 'steam_' . $steamId . '@example.com';
        $encryptedMail = EncryptManager::encrypt($mail);

        $user = UsersOAuthModel::getInstance()->getUser($steamId, $encryptedMail, $this->methodIdentifier());

        if (is_null($user)) {
            return $this->register();
        }

        // Login user
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_LOGIN;
    }

    private function extractSteamId(string $identity): ?string
    {
        // Extract SteamId from OpenID identity
        if (preg_match("/^https?:\/\/steamcommunity\.com\/openid\/id\/(\d+)$/", $identity, $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function getUserInfo(string $steamId): ?array
    {
        $url = 'http://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=' . $this->apiKey . '&steamids=' . $steamId;
        $response = file_get_contents($url);

        if ($response === false) {
            return null;
        }

        $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

        return $data['response']['players'][0] ?? null;
    }

    #[NoReturn] public function redirectToConsent(): void
    {
        $params = [
            'openid.ns' => 'http://specs.openid.net/auth/2.0',
            'openid.mode' => 'checkid_setup',
            'openid.return_to' => $this->redirectUri,
            'openid.realm' => EnvManager::getInstance()->getValue('PATH_URL'),
            'openid.identity' => 'http://specs.openid.net/auth/2.0/identifier_select',
            'openid.claimed_id' => 'http://specs.openid.net/auth/2.0/identifier_select',
        ];

        $authUrl = 'https://steamcommunity.com/openid/login?' . http_build_query($params);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
            <label for="oauth-steam-api-key">Steam API Key</label>
            <div class="input-group">
                <i class="fa-solid fa-key"></i>
                <input type="text" id="oauth-steam-api-key" 
                name="oauth-steam-api-key" 
                value="$this->apiKey"
                placeholder="API Key">
            </div>
            <small>Obtenez votre clé API sur <a href="https://steamcommunity.com/dev/apikey" target="_blank">Steam Dev</a></small>
HTML;
    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-steam-api-key'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'STEAM_API_KEY',
            FilterManager::filterInputStringPost('oauth-steam-api-key'),
        );
    }
}
