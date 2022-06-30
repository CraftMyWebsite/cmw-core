<?php

namespace CMW\Entity\Pages;


use CMW\Entity\Users\userEntity;

class pageEntity
{

    private int $pageId;
    private string $pageSlug;
    private string $pageTitle;
    private string $pageContent;
    private userEntity $pageUser;
    private string $pageConverted;
    private int $pageState;
    private string $pageCreated;
    private string $pageEdited;

    /**
     * @param int $pageId
     * @param string $pageSlug
     * @param string $pageTitle
     * @param string $pageContent
     * @param \CMW\Entity\Users\userEntity $pageUser
     * @param string $pageConverted
     * @param int $pageState
     * @param string $pageCreated
     * @param string $pageEdited
     */
     
    public function __construct(int $pageId, string $pageSlug, string $pageTitle, string $pageContent, userEntity $pageUser, string $pageConverted, int $pageState, string $pageCreated, string $pageEdited)
    {
        $this->pageId = $pageId;
        $this->pageSlug = $pageSlug;
        $this->pageTitle = $pageTitle;
        $this->pageContent = $pageContent;
        $this->pageUser = $pageUser;
        $this->pageConverted = $pageConverted;
        $this->pageState = $pageState;
        $this->pageCreated = $pageCreated;
        $this->pageEdited = $pageEdited;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->pageId;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->pageSlug;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->pageTitle;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->pageContent;
    }

    /**
     * @return \CMW\Entity\Users\userEntity
     */
    public function getUser(): userEntity
    {
        return $this->pageUser;
    }

    /**
     * @return string
     */
    public function getConverted(): string
    {
        return $this->pageConverted;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->pageState;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->pageCreated;
    }

    /**
     * @return string
     */
    public function getEdited(): string
    {
        return $this->pageEdited;
    }

}
