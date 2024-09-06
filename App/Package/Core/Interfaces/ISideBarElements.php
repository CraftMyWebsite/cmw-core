<?php

namespace CMW\Interface\Core;

interface ISideBarElements
{
    public function beforeWidgets(): void;
    public function afterWidgets(): void;
}
