<?php

namespace CMW\Entity\Pages;

class pagesEntity
{
    private int $pageId;
    private int $userId;
    private string $pageTitle;
    private string $pageSlug;
    private ?string $pageContent = null;
    private string $pageUpdated;
    private int $pageState;
    private string $pageContentTranslated;
    private string $pageCreated;

    /**
     * @param int $pageId
     * @param int $userId
     * @param string $pageTitle
     * @param string $pageSlug
     * @param string|null $pageContent
     * @param string $pageUpdated
     * @param int $pageState
     * @param string $pageContentTranslated
     * @param string|null $pageCreated
     */
    public function __construct(int $pageId, int $userId, string $pageTitle, string $pageSlug, ?string $pageContent, string $pageUpdated, int $pageState, string $pageContentTranslated, ?string $pageCreated)
    {
        $this->pageId = $pageId;
        $this->userId = $userId;
        $this->pageTitle = $pageTitle;
        $this->pageSlug = $pageSlug;
        $this->pageContent = $pageContent;
        $this->pageUpdated = $pageUpdated;
        $this->pageState = $pageState;
        $this->pageContentTranslated = $pageContentTranslated;
        $this->pageCreated = $pageCreated;
    }


    /**
     * @return int
     */
    public function getPageId(): int
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId(int $pageId): void
    {
        $this->pageId = $pageId;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * @return string
     */
    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * @param string $pageTitle
     */
    public function setPageTitle(string $pageTitle): void
    {
        $this->pageTitle = $pageTitle;
    }

    /**
     * @return string
     */
    public function getPageSlug(): string
    {
        return $this->pageSlug;
    }

    /**
     * @param string $pageSlug
     */
    public function setPageSlug(string $pageSlug): void
    {
        $this->pageSlug = $pageSlug;
    }

    /**
     * @return string|null
     */
    public function getPageContent(): ?string
    {
        return $this->pageContent;
    }

    /**
     * @param string|null $pageContent
     */
    public function setPageContent(?string $pageContent): void
    {
        $this->pageContent = $pageContent;
    }

    /**
     * @return string
     */
    public function getPageUpdated(): string
    {
        return $this->pageUpdated;
    }

    /**
     * @param string $pageUpdated
     */
    public function setPageUpdated(string $pageUpdated): void
    {
        $this->pageUpdated = $pageUpdated;
    }

    /**
     * @return int
     */
    public function getPageState(): int
    {
        return $this->pageState;
    }

    /**
     * @param int $pageState
     */
    public function setPageState(int $pageState): void
    {
        $this->pageState = $pageState;
    }

    /**
     * @return string
     */
    public function getPageContentTranslated(): string
    {
        return $this->pageContentTranslated;
    }

    /**
     * @param string $pageContentTranslated
     */
    public function setPageContentTranslated(string $pageContentTranslated): void
    {
        $this->pageContentTranslated = $pageContentTranslated;
    }

    /**
     * @return string
     */
    public function getPageCreated(): string
    {
        return $this->pageCreated;
    }

    /**
     * @param string $pageCreated
     */
    public function setPageCreated(string $pageCreated): void
    {
        $this->pageCreated = $pageCreated;
    }

}