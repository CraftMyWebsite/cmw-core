<?php

namespace CMW\Manager\Router;

use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Requests\HttpMethodsType;
use CMW\Utils\Arr;
use CMW\Utils\Website;

class Request extends AbstractManager
{
	private HttpMethodsType $method;
	private string $url;
	private array $headers;

	public function __construct()
	{
		$this->method = HttpMethodsType::fromName($_SERVER['REQUEST_METHOD']);
		$this->url = Website::getUrl();
	}

	/**
	 * @return HttpMethodsType
	 */
	public function getMethod(): HttpMethodsType
	{
		return $this->method;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @return array
	 */
	public function getHeaders(): array
	{
		if (empty($this->headers)) {
			$this->headers = getallheaders();
		}

		return $this->headers;
	}

	/**
	 * @param string $name
	 * @param string|null $defaultValue
	 * @return string|null
	 */
	public function getHeader(string $name, ?string $defaultValue = null): ?string
	{
		$headers = $this->getHeaders();
		$name = str_replace('-', '_', strtoupper($name));

		return $headers[$name] ?? $defaultValue;
	}


}