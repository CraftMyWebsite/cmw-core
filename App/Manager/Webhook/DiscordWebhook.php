<?php

namespace CMW\Manager\Webhook;


use JetBrains\PhpStorm\Pure;

class DiscordWebhook
{

    private ?string $url;
    private ?string $name;
    private ?string $content;

    // Embed
    private string $title;
    private string $description;
    private ?string $titleLink;
    private string $color;

    private string $footerText;
    private ?string $footerIconUrl;

    private ?string $imageUrl;

    private string $authorName;
    private ?string $authorUrl;

    private array $fields = [];

    //Optionnal (Text To Speech)
    private bool $tts = false;


    public function __construct(?string $url = null, ?string $name = null, ?string $content = null)
    {
        $this->url = $url;
        $this->name = $name;
        $this->content = $content;
    }


    /**
     * @param string $url
     * @return \CMW\Manager\Webhook\DiscordWebhook
     */
    #[Pure] public static function createWebhook(string $url): DiscordWebhook
    {
        return new self($url);
    }

    /**
     * @param string $name
     * @return DiscordWebhook
     */
    public function setWebhookName(string $name): DiscordWebhook
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $content
     * @return DiscordWebhook
     */
    public function setContent(string $content): DiscordWebhook
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string|null $url
     * @return DiscordWebhook
     */
    public function setUrl(?string $url): DiscordWebhook
    {
        $this->url = $url;
        return $this;
    }

    /**
     * @param string|null $name
     * @return DiscordWebhook
     */
    public function setName(?string $name): DiscordWebhook
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $title
     * @return DiscordWebhook
     */
    public function setTitle(string $title): DiscordWebhook
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param string $description
     * @return DiscordWebhook
     */
    public function setDescription(string $description): DiscordWebhook
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param string|null $titleLink
     * @return DiscordWebhook
     */
    public function setTitleLink(?string $titleLink): DiscordWebhook
    {
        $this->titleLink = $titleLink;
        return $this;
    }

    /**
     * @param string $color : hex color string
     * @return DiscordWebhook
     * @desc Provide hex color string without #, ex: a870fe
     */
    public function setColor(string $color): DiscordWebhook
    {
        $this->color = str_replace('#', '', $color);
        return $this;
    }

    /**
     * @param string $footerText
     * @return DiscordWebhook
     */
    public function setFooterText(string $footerText): DiscordWebhook
    {
        $this->footerText = $footerText;
        return $this;
    }

    /**
     * @param string|null $footerIconUrl
     * @return DiscordWebhook
     */
    public function setFooterIconUrl(?string $footerIconUrl): DiscordWebhook
    {
        $this->footerIconUrl = $footerIconUrl;
        return $this;
    }

    /**
     * @param string|null $imageUrl
     * @return DiscordWebhook
     */
    public function setImageUrl(?string $imageUrl): DiscordWebhook
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }

    /**
     * @param string $authorName
     * @return DiscordWebhook
     */
    public function setAuthorName(string $authorName): DiscordWebhook
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * @param string|null $authorUrl
     * @return DiscordWebhook
     */
    public function setAuthorUrl(?string $authorUrl): DiscordWebhook
    {
        $this->authorUrl = $authorUrl;
        return $this;
    }

    /**
     * @param array $fields
     * @return DiscordWebhook
     * @desc EX: ["name" => "Field 1", "value" => "Field 1", "inline" => false],
     */
    public function setFields(array $fields): DiscordWebhook
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * @param bool $tts
     * @return DiscordWebhook
     */
    public function setTts(bool $tts): DiscordWebhook
    {
        $this->tts = $tts;
        return $this;
    }


    /**
     * @return void
     * @desc Send the webhook message
     */
    public function send(): void
    {
        $timestamp = date("c");
        $data = [
            // Message
            "content" => $this->content,

            // Username
            "username" => $this->name,

            // Avatar URL.
            "avatar_url" => $this->imageUrl,

            // Text-to-speech
            "tts" => $this->tts,

            // Files upload
            // "file" => "",

            // Embeds Array
            "embeds" => [
                [
                    // Embed Title
                    "title" => $this->title,

                    // Embed Type
                    "type" => "rich",

                    // Embed Description
                    "description" => $this->description,

                    // URL of title link
                    "url" => $this->titleLink,

                    // Timestamp of embed must be formatted as ISO8601
                    "timestamp" => $timestamp,

                    // Embed left border color in HEX
                    "color" => hexdec($this->color),

                    // Footer
                    "footer" => [
                        "text" => $this->footerText,
                        "icon_url" => $this->footerIconUrl
                    ],

                    // Author
                    "author" => [
                        "name" => $this->authorName,
                        "url" => $this->authorUrl
                    ],

                    // Additional Fields array
                    "fields" => $this->fields,
                ],
            ],

        ];

        if (!empty($this->imageUrl)) {
            $data += ['embeds' =>
                ["image" => [
                    "url" => $this->imageUrl
                ]]];

        }

        $data = json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);


        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        curl_exec($ch);

        //curl_error($ch);

        curl_close($ch);
    }

}
