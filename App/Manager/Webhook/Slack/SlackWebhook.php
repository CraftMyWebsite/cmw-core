<?php

/**
 * Slack Webhook
 *
 * This class is inspired by => https://github.com/SimonBackx/Slack-PHP-Webhook
 */

namespace CMW\Manager\Webhook\Slack;

use Exception;

class SlackWebhook
{
    // WebhookUrl e.g. https://hooks.slack.com/services/XXXXXXXXX/XXXXXXXXX/XXXXXXXXXXXXXXXXXXXXXXXX
    public string $url;

    // Empty => Default username set in Slack Webhook integration settings
    public string $username;

    // Empty => Default channel set in Slack Webhook integration settings
    public string $channel;

    // Empty => Default icon set in Slack Webhook integration settings
    public string $iconUrl;

    // Empty => Default icon set in Slack Webhook integration settings
    public string $icon_emoji;

    // Unfurl links: automatically fetch and create attachments for URLs
    // Empty = default (false)
    public false|string $unfurlLinks = false;

    public function __construct($webhookUrl)
    {
        $this->url = $webhookUrl;
    }

    function __isset($property)
    {
        return isset($this->$property);
    }

    public function send(SlackMessageWebhook $message): bool
    {
        $data = $message->toArray();

        try {
            $json = json_encode($data);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $this->url,
                CURLOPT_USERAGENT => 'cURL Request',
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => ['payload' => $json],
            ]);
            $result = curl_exec($curl);

            if (!$result) {
                return false;
            }

            curl_close($curl);

            return $result === 'ok';
        } catch (Exception $e) {
            return false;
        }

    }

    public function setDefaultUnfurlLinks($unfurl): static
    {
        $this->unfurlLinks = $unfurl;
        return $this;
    }

    public function setDefaultChannel($channel): static
    {
        $this->channel = $channel;
        return $this;
    }

    public function setDefaultUsername($username): static
    {
        $this->username = $username;
        return $this;
    }

    public function setDefaultIcon($url): static
    {
        $this->iconUrl = $url;
        return $this;
    }

    public function setDefaultEmoji($emoji): static
    {
        $this->icon_emoji = $emoji;
        return $this;
    }
}