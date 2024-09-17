<?php

/**
 * Slack Webhook
 *
 * This class is inspired by => https://github.com/SimonBackx/Slack-PHP-Webhook
 */

namespace CMW\Manager\Webhook\Slack;

class SlackAttachmentFieldWebhook
{
    // Required
    public string $title = "";
    public string $value = "";

    // Optional
    public ?bool $short;

    public function __construct(string $title, string $value, ?bool $short = NULL)
    {
        $this->title = $title;
        $this->value = $value;
        if (isset($short)) {
            $this->short = $short;
        }
    }

    public function setShort(bool $bool = true): static
    {
        $this->short = $bool;
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'title' => $this->title,
            'value' => $this->value,
        ];
        if (isset($this->short)) {
            $data['short'] = $this->short;
        }
        return $data;
    }
}