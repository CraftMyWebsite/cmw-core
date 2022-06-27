<?php

namespace CMW\Entity\Pages;

<<<<<<< Updated upstream
use CMW\Model\Users\usersModel;
=======
use CMW\Entity\Users\userEntity;
>>>>>>> Stashed changes

class pageEntity
{

    private int $pageId;
    private string $pageSlug;
    private string $pageTitle;
    private string $pageContent;
<<<<<<< Updated upstream
    private usersModel $pageUser;
=======
    private userEntity $pageUser;
>>>>>>> Stashed changes
    private string $pageConverted;
    private int $pageState;
    private string $pageCreated;
    private string $pageEdited;

    /**
     * @param int $pageId
     * @param string $pageSlug
     * @param string $pageTitle
     * @param string $pageContent
<<<<<<< Updated upstream
     * @param \CMW\Model\Users\usersModel $pageUser
=======
     * @param \CMW\Entity\Users\userEntity $pageUser
>>>>>>> Stashed changes
     * @param string $pageConverted
     * @param int $pageState
     * @param string $pageCreated
     * @param string $pageEdited
     */
<<<<<<< Updated upstream
    public function __construct(int $pageId, string $pageSlug, string $pageTitle, string $pageContent, usersModel $pageUser, string $pageConverted, int $pageState, string $pageCreated, string $pageEdited)
=======
    public function __construct(int $pageId, string $pageSlug, string $pageTitle, string $pageContent, userEntity $pageUser, string $pageConverted, int $pageState, string $pageCreated, string $pageEdited)
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream
     * @return \CMW\Model\Users\usersModel
     */
    public function getUser(): usersModel
=======
     * @return \CMW\Entity\Users\userEntity
     */
    public function getUser(): userEntity
>>>>>>> Stashed changes
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
<<<<<<< Updated upstream

    


=======
>>>>>>> Stashed changes
}