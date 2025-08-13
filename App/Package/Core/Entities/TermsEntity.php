<?php

namespace CMW\Entity\Core;

use CMW\Entity\Users\UserEntity;
use CMW\Manager\Package\AbstractEntity;
use CMW\Type\Core\Enum\TermType;
use CMW\Utils\Date;

/**
 * Class: @TermsEntity
 * @package Core
 * @link https://craftmywebsite.fr/docs/fr/technical/creer-un-package/entities
 */
class TermsEntity extends AbstractEntity
{
   private int $id;
   private TermType $type;
   private string $content;
   private int $requireAccept;
   private string $publishedAt;
   private ?UserEntity $lastEditor;

    /**
     * @param int $id
     * @param TermType $type
     * @param string $content
     * @param int $requireAccept
     * @param string $publishedAt
     * @param ?UserEntity $lastEditor
     */
    public function __construct(int $id, TermType $type, string $content, int $requireAccept, string $publishedAt, ?UserEntity $lastEditor)
    {
        $this->id = $id;
        $this->type = $type;
        $this->content = $content;
        $this->requireAccept = $requireAccept;
        $this->publishedAt = $publishedAt;
        $this->lastEditor = $lastEditor;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): TermType
    {
        return $this->type;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getRequireAccept(): int
    {
        return $this->requireAccept;
    }

    public function getPublishedAt(): string
    {
        return Date::formatDate($this->publishedAt);
    }

    public function getLastEditor(): ?UserEntity
    {
        return $this->lastEditor;
    }


}
