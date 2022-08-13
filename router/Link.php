<?php

namespace CMW\Router;

use Attribute;
use JetBrains\PhpStorm\ExpectedValues;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Link
{
    public const GET = "get";
    public const POST = "post";

    public function __construct(private readonly string                                                $path,
                                #[ExpectedValues(flagsFromClass: Link::class)] private readonly string $method,
                                private readonly array                                                 $variables = array(),
                                private readonly ?string                                               $scope = null,
                                private readonly int                                                   $weight = 1,
                                private readonly ?string                                               $name = null)
    {
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables;
    }

    /**
     * @return string|null
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

}