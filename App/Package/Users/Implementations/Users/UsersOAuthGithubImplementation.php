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
use CMW\Utils\Website;
use Exception;
use JetBrains\PhpStorm\NoReturn;
use function is_null;

class UsersOAuthGithubImplementation implements IUsersOAuth
{
    private ?string $clientId;
    private ?string $clientSecret;
    private ?string $redirectUri;

    public function __construct()
    {
        $env = EnvManager::getInstance();
        $this->clientId = $env->getValue('OAUTH_GITHUB_CLIENT_ID');
        $this->clientSecret = $env->getValue('OAUTH_GITHUB_CLIENT_SECRET');
        $this->redirectUri = $env->getValue('PATH_URL') . 'api/oauth/github/connect';
    }

    public function methodeName(): string
    {
        return "GitHub";
    }

    public function methodIdentifier(): string
    {
        return "github";
    }

    public function methodeIconLink(): string
    {
        return EnvManager::getInstance()->getValue('PATH_URL')
            . 'App/Package/Users/Implementations/Users/Assets/OAuth/github.png';
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

        $mail = $userInfo['email'] ?? null;
        $id = $userInfo['id'];

        // If email is not provided (which can happen with GitHub), generate one using the user's ID
        $encryptedMail = EncryptManager::encrypt($mail ?: "user-$id@github.com");

        // If account already exists, log in the user
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (!is_null($user)) {
            UsersLoginController::getInstance()->loginUser($user, true);
            return OAuthLoginStatus::SUCCESS_LOGIN;
        }

        // Check if email is already used in the system
        if ($mail && UsersModel::getInstance()->checkEmail($encryptedMail)) {
            return OAuthLoginStatus::EMAIL_ALREADY_EXIST;
        }

        // Create a pseudo from the username or email
        $pseudo = ucfirst(Utils::normalizeForSlug($userInfo['login']));

        // If the pseudo already exists, add a random string
        if (UsersModel::getInstance()->checkPseudo($pseudo)) {
            $pseudo .= Utils::generateRandomNumber(3);
        }

        // Create a new user
        $user = UsersModel::getInstance()->create(
            $encryptedMail,
            $pseudo,
            $userInfo['name'] ?? null,
            null,
            RolesController::getInstance()->getDefaultRolesId(),
        );

        if (!$user) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_USER;
        }

        if (!UsersOAuthModel::getInstance()->createUser($user->getId(), $id, $this->methodIdentifier())) {
            return OAuthLoginStatus::UNABLE_TO_CREATE_OAUTH_USER;
        }

        // Download and set the user's GitHub profile picture
        $imageLink = $userInfo['avatar_url'];

        try {
            $imageName = ImagesManager::downloadFromLink($imageLink, 'Users');
            UserPictureModel::getInstance()->uploadImage($user->getId(), $imageName);
        } catch (Exception) {
        }

        // Log in the user
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
        $mail = $userInfo['email'] ?? "user-$id@github.com";
        $encryptedMail = EncryptManager::encrypt($mail);

        // If account doesn't exist, register the user
        $user = UsersOAuthModel::getInstance()->getUser($id, $encryptedMail, $this->methodIdentifier());

        if (is_null($user)) {
            return $this->register();
        }

        // Log in the user
        UsersLoginController::getInstance()->loginUser($user, true);

        return OAuthLoginStatus::SUCCESS_LOGIN;
    }

    /**
     * @throws \JsonException
     */
    private function getAccessToken(string $code): array
    {
        $url = 'https://github.com/login/oauth/access_token';

        $data = [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri,
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\nAccept: application/json\r\n",
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
        $url = 'https://api.github.com/user';

        $options = [
            'http' => [
                'header' => "Authorization: token " . $token['access_token'] . "\r\nUser-Agent: " . Website::getWebsiteName(),
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
        $authUrl = 'https://github.com/login/oauth/authorize?' . http_build_query([
                'response_type' => 'code',
                'client_id' => $this->clientId,
                'redirect_uri' => $this->redirectUri,
                'scope' => 'user:email',
            ]);

        header('Location: ' . $authUrl);
        exit();
    }

    public function adminForm(): void
    {
        echo <<<HTML
            <label for="oauth-github-client-id">GitHub Client ID</label>
            <div class="input-group">
                <i class="fa-solid fa-id-card"></i>
                <input type="text" id="oauth-github-client-id" 
                name="oauth-github-client-id" 
                value="$this->clientId"
                placeholder="client_id">
            </div>
            
            <label for="oauth-github-client-secret">GitHub Client Secret</label>
            <div class="input-group">
                <i class="fa-solid fa-lock"></i>
                <input type="text" id="oauth-github-client-secret" 
                name="oauth-github-client-secret" 
                value="$this->clientSecret"
                placeholder="client_secret">
            </div>
            
            <small>Plus d'informations sur notre <a href="#" target="_blank">WIKI</a></small>
HTML;
    }

    public function adminFormPost(): void
    {
        if (!isset($_POST['oauth-github-client-id'], $_POST['oauth-github-client-secret'])) {
            return;
        }

        $env = EnvManager::getInstance();
        $env->setOrEditValue(
            'OAUTH_GITHUB_CLIENT_ID',
            FilterManager::filterInputStringPost('oauth-github-client-id'),
        );
        $env->setOrEditValue(
            'OAUTH_GITHUB_CLIENT_SECRET',
            FilterManager::filterInputStringPost('oauth-github-client-secret'),
        );
    }
}