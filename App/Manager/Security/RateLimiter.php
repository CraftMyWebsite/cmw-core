<?php


namespace CMW\Manager\Security;


class RateLimiter
{
    private string $cookieName = "cmw_rate_limit";
    private int $maxCount = 30;

    public function __construct()
    {
        $this->logic();
    }

    /**
     * @return void
     * @desc Check rate limit and return 429 if spam is detected
     */
    private function logic(): void
    {
        if ($this->getCount() >= $this->maxCount) {
            http_response_code(429);
            die();
        }

        $this->increaseCounter();
    }

    /**
     * @return int
     * @desc Return count
     */
    private function getCount(): int
    {
        return isset($_COOKIE[$this->cookieName]) ? (int)$_COOKIE[$this->cookieName] : 0;
    }

    /**
     * @return void
     * @desc Increment counter
     */
    private function increaseCounter(): void
    {
        setcookie($this->cookieName, $this->getCount() + 1, time() + 60, "/", false, true);
    }

}