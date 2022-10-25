<?php

namespace CMW\Manager\Api;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class APIRoute
{
    public function __construct()
    {
    }
}