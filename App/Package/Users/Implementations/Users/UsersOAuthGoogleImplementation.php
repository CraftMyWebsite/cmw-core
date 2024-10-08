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

class UsersOAuthGoogleImplementation implements IUsersOAuth
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $redirectUri;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->clientId = $env->getValue('OAUTH_GOOGLE_CLIENT_ID');
        $this->clientSecret = $env->getValue('OAUTH_GOOGLE_CLIENT_SECRET');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/google/connect';
    }

    public function methodeName(): string
    {
        return "Google";
    }

    public function methodIdentifier(): string
    {
        return "google";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/google.png';
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

        $pseudo = ucfirst(Utils::normalizeForSlug(explode('@', $mail)[0]));

        //If pseudo already exist, we add a random id after the pseudo
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

        $imageLink = $userInfo['picture'];

        try {
            $imageName = ImagesManager::downloadFromLink($imageLink, 'Users');
            UserPictureModel::getInstance()->uploadImage($user->getId(), $imageName);
        } catch (Exception) {
        }

        //Login user
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
        $url = 'https://oauth2.googleapis.com/token';

        $data = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
            'grant_type' => 'authorization_code',
            'scope' => 'email',
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
    private
    function getUserInfo($token)
    {
        $url = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token['access_token'];

        $response = file_get_contents($url);

        if ($response === false) {
            return null;
        }

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    #[NoReturn] public function redirectToConsent(): void
    {
        $authUrl = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile',
                'access_type' => 'offline',
                'prompt' => 'consent',
            ]);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
            <label for="oauth-google-client-id">Google Client ID</label>
            <div class="input-group">
                <i class="fa-solid fa-id-card"></i>
                <input type="text" id="oauth-google-client-id" 
                name="oauth-google-client-id" 
                value="$this->clientId"
                placeholder="client_id">
            </div>
            
            <label for="oauth-google-client-secret">Google Client Secret</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="text" id="oauth-google-client-secret" 
                name="oauth-google-client-secret" 
                value="$this->clientSecret"
                placeholder="client_secret">
            </div>
            
            <small>Plus d'informations sur notre <a href="#" target="_blank">WIKI</a></small>
HTML;
    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-google-client-id'], $_POST['oauth-google-client-secret'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'OAUTH_GOOGLE_CLIENT_ID',
            FilterManager::filterInputStringPost('oauth-google-client-id'),
        );
        $env->setOrEditValue(
            'OAUTH_GOOGLE_CLIENT_SECRET',
            FilterManager::filterInputStringPost('oauth-google-client-secret'),
        );
    }
}
