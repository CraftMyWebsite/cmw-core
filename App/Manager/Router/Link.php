<?php

namespace CMW\Manager\Router;

use JetBrains\PhpStorm\ExpectedValues;
use Attribute;
use function str_contains;

#[Attribute(Attribute::TARGET_METHOD | Attribute::IS_REPEATABLE)]
class Link
{
    public const GET = 'get';
    public const POST = 'post';

    /**
     * @param string $path
     * @param string $method
     * @param array $variables
     * @param string|null $scope
     * @param int $weight
     * @param string|null $name
     * @param bool $secure
     */
    public function __construct(
        private readonly string $path,

        #[ExpectedValues(flagsFromClass: Link::class)]
        private readonly string $method,

        private readonly array $variables = array(),
        private readonly ?string $scope = null,
        private readonly int $weight = 1,
        private ?string $name = null,
        private readonly bool $secure = true
    ) {}

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

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return bool
     */
    public function isSecure(): bool
    {
        return $this->secure;
    }

    /**
     * <p>Check if the route contains 'cmw-admin'</p>
     * @return bool
     */
    public function isAdmin(): bool
    {
        return str_contains($this->path, 'cmw-admin') || str_contains($this->scope, 'cmw-admin');
    }
}
