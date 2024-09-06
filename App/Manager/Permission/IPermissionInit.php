<?php

namespace CMW\Manager\Permission;

interface IPermissionInit
{
    /**
     * @return \CMW\Manager\Permission\PermissionInitType[]
     */
    public function permissions(): array;
}
