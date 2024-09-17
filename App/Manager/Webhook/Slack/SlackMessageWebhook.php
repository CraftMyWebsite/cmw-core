<?php

/**
 * Slack Webhook
 *
 * This class is inspired by => https://github.com/SimonBackx/Slack-PHP-Webhook
 */


namespace CMW\Manager\Webhook\Slack;

class SlackMessageWebhook
{
    private SlackWebhook $slack;

    // Message to post
    public string $text = "";

    // Empty => Default username set in Slack instance
    public string $username;

    // Empty => Default channel set in Slack instance
    public string $channel;

    // Empty => Default icon set in Slack instance
    public string $iconUrl;

    // Empty => Default icon set in Slack instance
    public string $iconEmoji;

    public false|string $unfurlLinks;

    // Array of SlackAttachment instances
    /*  */
    public array $attachments;

    public function __construct(SlackWebhook $slack)
    {
        $this->slack = $slack;
    }

    /*
    Settings
     */
    public function setText($text): static
    {
        $this->text = $text;
        return $this;
    }

    public function setUsername($username): static
    {
        $this->username = $username;
        return $this;
    }

    public function setChannel($channel): static
    {
        $this->channel = $channel;
        return $this;
    }

    public function setEmoji($emoji): static
    {
        $this->iconEmoji = $emoji;
        return $this;
    }

    public function setIcon($url): static
    {
        $this->iconUrl = $url;
        return $this;
    }

    public function setUnfurlLinks($bool): static
    {
        $this->unfurlLinks = $bool;
        return $this;
    }

    public function addAttachment(SlackAttachment $attachment): static
    {
        if (!isset($this->attachments)) {
            $this->attachments = [$attachment];
            return $this;
        }

        $this->attachments[] = $attachment;
        return $this;
    }

    public function toArray(): array
    {
        // Loading defaults
        if (isset($this->slack->username)) {
            $username = $this->slack->username;
        }

        if (isset($this->slack->channel)) {
            $channel = $this->slack->channel;
        }

        if (isset($this->slack->icon_url)) {
            $icon_url = $this->slack->icon_url;
        }

        if (isset($this->slack->icon_emoji)) {
            $icon_emoji = $this->slack->icon_emoji;
        }

        if (isset($this->slack->unfurl_links)) {
            $unfurl_links = $this->slack->unfurl_links;
        }

        // Overwrite/create defaults
        if (isset($this->username)) {
            $username = $this->username;
        }

        if (isset($this->channel)) {
            $channel = $this->channel;
        }

        if (isset($this->iconUrl)) {
            $icon_url = $this->iconUrl;
        }

        if (isset($this->iconEmoji)) {
            $icon_emoji = $this->iconEmoji;
        }

        if (isset($this->unfurlLinks)) {
            $unfurl_links = $this->unfurlLinks;
        }

        $data = [
            'text' => $this->text,
        ];
        if (isset($username)) {
            $data['username'] = $username;
        }

        if (isset($channel)) {
            $data['channel'] = $channel;
        }

        if (isset($icon_url)) {
            $data['icon_url'] = $icon_url;
        } else if (isset($icon_emoji)) {
            $data['icon_emoji'] = $icon_emoji;
        }

        if (isset($unfurl_links)) {
            $data['unfurl_links'] = $unfurl_links;
        }

        if (isset($this->attachments)) {
            $attachments = [];
            foreach ($this->attachments as $attachment) {
                $attachments[] = $attachment->toArray();
            }
            $data['attachments'] = $attachments;
        }
        return $data;
    }

    /*
     * Send this message to Slack
     */
    public function send(): bool
    {
        return $this->slack->send($this);
    }
}