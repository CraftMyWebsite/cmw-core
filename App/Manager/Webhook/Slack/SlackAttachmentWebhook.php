<?php

/**
 * Slack Webhook
 *
 * This class is inspired by => https://github.com/SimonBackx/Slack-PHP-Webhook
 */


namespace CMW\Manager\Webhook\Slack;

class SlackAttachmentWebhook
{
    // Required
    public string $fallback = "";

    // Optionals
    public string $color;
    public string $pretext;
    public ?string $authorName;
    public ?string $authorIcon;
    public string $authorLink;
    public string $title;
    public ?string $titleLink;
    public string $text;
    public array $fields;
    public mixed $mrkdwnInFields;
    public string $imageUrl;
    public string $thumbUrl;

    // Footer
    public string $footer;
    public string $footerIcon;
    public int $ts;

    // Actions
    public array $actions;

    public function __construct(string $fallback)
    {
        $this->fallback = $fallback;
    }

    /**
     * Accepted values: "good", "warning", "danger" or any hex color code
     */
    public function setColor(string $color): static
    {
        $this->color = $color;
        return $this;
    }

    public function setText(string $text): static
    {
        $this->text = $text;
        return $this;
    }

    /**
     * Optional text that appears above the attachment block
     */
    public function setPretext(string $pretext): static
    {
        $this->pretext = $pretext;
        return $this;
    }

    /**
     * The author parameters will display a small section at the top of a message attachment.
     * @param string $authorName [description]
     * @param string|null $authorLink A valid URL that will hyperlink the author_name text mentioned above. Set to NULL to ignore this value.
     * @param string|null $authorIcon A valid URL that displays a small 16x16px image to the left of the author_name text. Set to NULL to ignore this value.
     * @return \CMW\Manager\Webhook\Slack\SlackAttachmentWebhook
     */
    public function setAuthor(string $authorName, ?string $authorLink = NULL, ?string $authorIcon = NULL): static
    {
        $this->setAuthorName($authorName);
        if (isset($authorLink)) {
            $this->setAuthorLink($authorLink);
        }

        if (isset($authorIcon)) {
            $this->setAuthorIcon($authorIcon);
        }

        return $this;
    }

    public function setAuthorName(?string $authorName): static
    {
        $this->authorName = $authorName;
        return $this;
    }

    /**
     * Enable text formatting for: "pretext", "text" or "fields".
     * Setting "fields" will enable markup formatting for the value of each field.
     */
    public function enableMarkdownFor(mixed $mrkdwnInFields): static
    {
        if (!isset($this->mrkdwnInFields)) {
            $this->mrkdwnInFields = [$mrkdwnInFields];
            return $this;
        }
        $this->mrkdwnInFields[] = $mrkdwnInFields;
        return $this;
    }

    /**
     * A valid URL that displays a small 16x16px image to the left of the author_name text.
     */
    public function setAuthorIcon($authorIcon): static
    {
        $this->authorIcon = $authorIcon;
        return $this;
    }

    /**
     * A valid URL that will hyperlink the author_name text mentioned above.
     */
    public function setAuthorLink($authorLink): static
    {
        $this->authorLink = $authorLink;
        return $this;
    }

    /**
     * The title is displayed as larger, bold text near the top of a message attachment.
     * @param string $title
     * @param string|null $link By passing a valid URL in the link parameter (optional), the
     * title text will be hyperlinked.
     * @return \CMW\Manager\Webhook\Slack\SlackAttachmentWebhook
     */
    public function setTitle(string $title, ?string $link = NULL): static
    {
        $this->title = $title;
        if (isset($link)) {
            $this->titleLink = $link;
        }
        return $this;
    }

    /**
     * A valid URL to an image file that will be displayed inside a message attachment. We currently
     *  support the following formats: GIF, JPEG, PNG, and BMP.
     *
     *  Large images will be resized to a maximum width of 400px or a maximum height of 500px, while
     *   still maintaining the original aspect ratio.
     * @param string $url
     * @return \CMW\Manager\Webhook\Slack\SlackAttachmentWebhook
     */
    public function setImage(string $url): static
    {
        $this->imageUrl = $url;
        return $this;
    }

    /**
     * A valid URL to an image file that will be displayed as a thumbnail on the right side of a
     * message attachment. We currently support the following formats: GIF, JPEG, PNG, and BMP.
     *
     * The thumbnail's longest dimension will be scaled down to 75px while maintaining the aspect
     * ratio of the image. The filesize of the image must also be less than 500 KB.
     *
     * For best results, please use images that are already 75px by 75px.
     * @param string $url HTTP url of the thumbnail
     */
    public function setThumbnail(string $url): static
    {
        $this->thumbUrl = $url;
        return $this;
    }

    /**
     * Add some brief text to help contextualize and identify an attachment. Limited to 300
     * characters, and may be truncated further when displayed to users in environments with limited
     *  screen real estate.
     * @param string $text max 300 characters
     */
    public function setFooterText(string $text): static
    {
        $this->footer = $text;
        return $this;
    }

    /**
     * To render a small icon beside your footer text, provide a publicly accessible URL string in
     * the footer_icon field. You must also provide a footer for the field to be recognized.
     *
     * We'll render what you provide at 16px by 16px. It's best to use an image that is similarly
     * sized.
     * @param string $url 16x16 image url
     */
    public function setFooterIcon(string $url): static
    {
        $this->footerIcon = $url;
        return $this;
    }

    /**
     * Does your attachment relate to something happening at a specific time?
     *
     * By providing the ts field with an integer value in "epoch time", the attachment will display
     * an additional timestamp value as part of the attachment's footer. Use ts when referencing
     * articles or happenings. Your message will have its own timestamp when published.
     *
     * Example: Providing 123456789 would result in a rendered timestamp of Nov 29th, 1973.
     * @param int $timestamp Integer value in "epoch time"
     */
    public function setTimestamp(int $timestamp): static
    {
        $this->ts = $timestamp;
        return $this;
    }

    public function addFieldInstance(SlackAttachmentFieldWebhook $field): static
    {
        if (!isset($this->fields)) {
            $this->fields = [$field];
            return $this;
        }
        $this->fields[] = $field;
        return $this;
    }

    /**
     * Shortcut without defining SlackAttachmentField
     */
    public function addField(string $title, string $value, ?bool $short = NULL): static
    {
        return $this->addFieldInstance(new SlackAttachmentFieldWebhook($title, $value, $short));
    }

    private function addAction($action): void
    {
        if (!isset($this->actions)) {
            $this->actions = [$action];
            return;
        }
        $this->actions[] = $action;
    }

    /**
     * @param string $text A UTF-8 string label for this button. Be brief but descriptive and
     * actionable.
     * @param string $url The fully qualified http or https URL to deliver users to. Invalid URLs
     * will result in a message posted with the button omitted
     * @param ?string $style (optional) Setting to primary turns the button green and indicates the
     * best forward action to take. Providing danger turns the button red and indicates it some kind
     *  of destructive action. Use sparingly. Be default, buttons will use the UI's default text
     *  color.
     */
    public function addButton(string $text, string $url, ?string $style = null): static
    {
        $action = (object)[
            "type" => "button",
            "text" => $text,
            "url" => $url,
        ];
        if (isset($style)) {
            $action->style = $style;
        }
        $this->addAction($action);
        return $this;
    }

    public function toArray(): array
    {
        $data = [
            'fallback' => $this->fallback,
        ];
        if (isset($this->color)) {
            $data['color'] = $this->color;
        }

        if (isset($this->pretext)) {
            $data['pretext'] = $this->pretext;
        }

        if (isset($this->authorName)) {
            $data['author_name'] = $this->authorName;
        }

        if (isset($this->mrkdwnInFields)) {
            $data['mrkdwn_in'] = $this->mrkdwnInFields;
        }

        if (isset($this->authorLink)) {
            $data['author_link'] = $this->authorLink;
        }

        if (isset($this->authorIcon)) {
            $data['author_icon'] = $this->authorIcon;
        }

        if (isset($this->title)) {
            $data['title'] = $this->title;
        }

        if (isset($this->titleLink)) {
            $data['title_link'] = $this->titleLink;
        }

        if (isset($this->text)) {
            $data['text'] = $this->text;
        }

        if (isset($this->fields)) {
            $fields = [];
            foreach ($this->fields as $field) {
                $fields[] = $field->toArray();
            }
            $data['fields'] = $fields;
        }

        if (isset($this->imageUrl)) {
            $data['image_url'] = $this->imageUrl;
        }

        if (isset($this->thumbUrl)) {
            $data['thumb_url'] = $this->thumbUrl;
        }

        if (isset($this->footer)) {
            $data['footer'] = $this->footer;
        }

        if (isset($this->footerIcon)) {
            $data['footer_icon'] = $this->footerIcon;
        }

        if (isset($this->ts)) {
            $data['ts'] = $this->ts;
        }

        if (isset($this->actions)) {
            $data['actions'] = (array)$this->actions;
        }

        return $data;
    }
}