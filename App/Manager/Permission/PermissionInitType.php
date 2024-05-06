<?php


namespace CMW\Manager\Permission;

class  PermissionInitType
{
    private string $code;
    private string $description;

    /**
     * @param string $code Ex: "core.dashboard"
     * @param string $description Ex: "Dashboard access"
     */
    public function __construct(string $code, string $description)
    {
        $this->code = $code;
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}