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
use function explode;
use function is_null;

class UsersOAuthFacebookImplementation implements IUsersOAuth
{
    private ?string $appId;
    private ?string $appSecret;
    private ?string $redirectUri;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->appId = $env->getValue('OAUTH_FACEBOOK_APP_ID');
        $this->appSecret = $env->getValue('OAUTH_FACEBOOK_APP_SECRET');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/facebook/connect';
    }

    public function methodeName(): string
    {
        return "Facebook";
    }

    public function methodIdentifier(): string
    {
        return "facebook";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/facebook.png';
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
            $userInfo['first_name'] ?? null,
            $userInfo['last_name'] ?? null,
            RolesController::getInstance()->getDefaultRolesId(),
        );

        if (!$user) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_USER;
        }

        if (!UsersOAuthModel::getInstance()->createUser($user->getId(), $id, $this->methodIdentifier())) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_OAUTH_USER;
        }

        // Connexion de l'utilisateur
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

        // Si le compte n'existe pas, on enregistre l'utilisateur...
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (is_null($user)) {
            return $this->register();
        }

        // Connexion de l'utilisateur
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_LOGIN;
    }

    private function getAccessToken(string $code): ?string
    {
        $url = 'https://graph.facebook.com/v10.0/oauth/access_token?' . http_build_query([
                'client_id' => $this->appId,
                'redirect_uri' => $this->redirectUri,
                'client_secret' => $this->appSecret,
                'code' => $code,
            ]);

        $result = file_get_contents($url);
        if ($result === false) {
            return null;
        }

        $data = json_decode($result, true);
        return $data['access_token'] ?? null;
    }

    private function getUserInfo($token)
    {
        $url = 'https://graph.facebook.com/me?fields=id,name,email&access_token=' . $token;

        $response = file_get_contents($url);

        if ($response === false) {
            return null;
        }

        return json_decode($response, true);
    }

    #[NoReturn] public function redirectToConsent(): void
    {
        $authUrl = 'https://www.facebook.com/v10.0/dialog/oauth?' . http_build_query([
                'client_id' => $this->appId,
                'redirect_uri' => $this->redirectUri,
                'scope' => 'email',
                'response_type' => 'code',
            ]);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
            <label for="oauth-facebook-app-id">Facebook App ID</label>
            <div class="input-group">
                <i class="fa-solid fa-id-card"></i>
                <input type="text" id="oauth-facebook-app-id" 
                name="oauth-facebook-app-id" 
                value="$this->appId"
                placeholder="App ID">
            </div>
            
            <label for="oauth-facebook-app-secret">Facebook App Secret</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="text" id="oauth-facebook-app-secret" 
                name="oauth-facebook-app-secret" 
                value="$this->appSecret"
                placeholder="App Secret">
            </div>
            
            <small>Plus d'informations sur notre <a href="#" target="_blank">WIKI</a></small>
HTML;
    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-facebook-app-id'], $_POST['oauth-facebook-app-secret'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'OAUTH_FACEBOOK_APP_ID',
            FilterManager::filterInputStringPost('oauth-facebook-app-id'),
        );
        $env->setOrEditValue(
            'OAUTH_FACEBOOK_APP_SECRET',
            FilterManager::filterInputStringPost('oauth-facebook-app-secret'),
        );
    }
}
