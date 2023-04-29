<?php

namespace CMW\Manager\Requests;

use JetBrains\PhpStorm\ExpectedValues;

class Request
{

    private string $url;
    private string $methode;
    private array $params;
    private array $data;
    private string $emitUrl;

    /**
     * @param string $url
     * @param string $methode
     * @param array $params
     * @param array $data
     * @param string $emitUrl
     */
    public function __construct(string $url, #[ExpectedValues(['GET', 'POST'])] string $methode, array $params,
                                array  $data, string $emitUrl)
    {
        $this->url = $url;
        $this->methode = $methode;
        $this->params = $params;
        $this->data = $data;
        $this->emitUrl = $emitUrl;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethode(): string
    {
        return $this->methode;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getEmitUrl(): string
    {
        return $this->emitUrl;
    }
}