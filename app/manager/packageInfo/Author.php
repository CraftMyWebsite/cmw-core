<?php

namespace CMW\Manager\PackageInfo;

use JetBrains\PhpStorm\ArrayShape;

class Author
{

    public function __construct(
        private readonly string $name,
        #[ArrayShape(["mail" => "?string", "discord" => "?string", "twitter" => "?string"])]
        private readonly ?array $social = null
    )
    {
    }

    public function getName(): string
    {
        return $this->name;
    }

    #[ArrayShape(["mail" => "\?string", "discord" => "\?string", "twitter" => "\?string"])]
    public function getSocial(): ?array
    {
        return $this->social;
    }


}