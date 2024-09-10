<?php

namespace CMW\Manager\Components\Base;

use CMW\Manager\Components\IComponent;
use CMW\Manager\Security\SecurityManager;
use JetBrains\PhpStorm\ExpectedValues;

class FormComponentBase extends IComponent
{

    private string $method = "POST";
    private string $action = "";
    private string $enctype = "application/x-www-form-urlencoded";
    private string $name = "";
    private string $onSubmit = "";
    /* @var IComponent[] $children */
    private array $children = [];

    /**
     * @param string $method
     * @return FormComponentBase
     */
    public function setMethod(#[ExpectedValues(['post', 'get', 'dialog'])] string $method): FormComponentBase
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $action
     * @return FormComponentBase
     */
    public function setAction(string $action): FormComponentBase
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param string $enctype
     * @return FormComponentBase
     */
    public function setEnctype(
        #[ExpectedValues(['multipart/form-data', 'application/x-www-form-urlencoded', 'text/plain'])]
        string $enctype,
    ): FormComponentBase
    {
        $this->enctype = $enctype;
        return $this;
    }

    /**
     * @param string $name
     * @return FormComponentBase
     */
    public function setName(string $name): FormComponentBase
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string $onSubmit
     * @return FormComponentBase
     */
    public function setOnSubmit(string $onSubmit): FormComponentBase
    {
        $this->onSubmit = $onSubmit;
        return $this;
    }

    /**
     * @param IComponent[] $children
     * @return FormComponentBase
     */
    public function setChildren(array $children): FormComponentBase
    {
        $this->children = $children;
        return $this;
    }

    /**
     * @return void
     */
    public function printChildren(): void
    {
        foreach ($this->children as $child) {
            $child->render();
        }
    }

    public function render(): void
    {
        print "<form {$this->showId()}
                    method='$this->method' 
                    action='$this->action' 
                    enctype='$this->enctype' 
                    name='$this->name'
                    class='$this->classes'
                    onsubmit='$this->onSubmit'>";
        (new SecurityManager())->insertHiddenToken();
        $this->printChildren();
        print "</form>";
    }
}