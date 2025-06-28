<?php

namespace CMW\Manager\Http;

use Closure;
use CMW\Manager\Manager\AbstractManager;
use CMW\Manager\Requests\HttpMethodsType;
use CMW\Utils\File;
use CMW\Utils\Str;
use CurlHandle;
use JsonException;
use RuntimeException;
use stdClass;
use function http_build_query;
use function is_array;
use function is_object;
use function json_decode;
use const JSON_THROW_ON_ERROR;

/**
 * Class: @HttpManager
 * @manager Http
 */
class HttpManager extends AbstractManager
{
    public static array $defaultsOptions = [
        'method' => 'GET',
        'headers' => [],
        'timeout' => 30,
        'encoding' => 'utf-8',
        'body' => true,
        'follow' => true,
    ];

    public string|null $content = null;
    public CurlHandle|false $curl;
    public array $curlOpt = [];
    public int $errorCode;
    public string $errorMessage;
    public array $headers = [];
    public array $info = [];
    public array $options = [];

    public function __construct(string $url, array $options = [])
    {
        $defaults = static::$defaultsOptions;

        $this->options = array_merge($defaults, $options);
        $this->options['url'] = $url;

        $this->fetch();
    }

    public function __call(string $method, array $arguments = [])
    {
        $method = str_replace('-', '_', Str::kebab($method));
        return $this->info[$method] ?? null;
    }

    public static function __callStatic(string $method, array $arguments = []): static
    {
        return new static(
            url: $arguments[0],
            options: array_merge(
                ['method' => strtoupper($method)],
                $arguments[1] ?? []
            )
        );
    }

    /**
     * <p>Fetch the request</p>
     * @return $this
     */
    public function fetch(): static
    {
        // curl options
        $this->curlOpt = [
            CURLOPT_URL => $this->options['url'],
            CURLOPT_ENCODING => $this->options['encoding'],
            CURLOPT_CONNECTTIMEOUT => $this->options['timeout'],
            CURLOPT_TIMEOUT => $this->options['timeout'],
            CURLOPT_AUTOREFERER => true,
            CURLOPT_RETURNTRANSFER => $this->options['body'],
            CURLOPT_FOLLOWLOCATION => $this->options['follow'] ?? true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_HEADER => false,
            CURLOPT_HEADERFUNCTION => $this->prepareHeaders(),
        ];

        // Set Progress
        if (is_callable($this->options['progress']) === true) {
            $this->curlOpt[CURLOPT_NOPROGRESS] = false;
            $this->curlOpt[CURLOPT_PROGRESSFUNCTION] = $this->options['progress'];
        }

        // Add headers
        if (empty($this->options['headers']) === false) {
            $headers = [];
            foreach ($this->options['headers'] as $key => $value) {
                if (is_string($key) === true) {
                    $value = $key . ': ' . $value;
                }

                $headers[] = $value;
            }

            $this->curlOpt[CURLOPT_HTTPHEADER] = $headers;
        }

        // Set agent
        if (empty($this->options['agent']) === false) {
            $this->curlOpt[CURLOPT_USERAGENT] = $this->options['agent'];
        }

        // Prepare for specific methods
        $this->prepareCurlOptions();

        if ($this->options['test'] === true) {
            return $this;
        }

        // Start curl request
        $this->curl = curl_init();

        curl_setopt_array($this->curl, $this->curlOpt);

        $this->content = curl_exec($this->curl);
        $this->info = curl_getinfo($this->curl);
        $this->errorCode = curl_errno($this->curl);
        $this->errorMessage = curl_error($this->curl);

        if ($this->errorCode) {
            throw new RuntimeException($this->errorMessage, $this->errorCode);
        }

        curl_close($this->curl);

        return $this;
    }

    /**
     * @param string $url
     * @param array $params
     * @return static
     */
    public static function request(string $url, array $params = []): static
    {
        return new static($url, $params);
    }

    /**
     * <p>Send a simple GET request</p>
     * @param string $url
     * @param array $params
     * @return static
     */
    public static function get(string $url, array $params = []): static
    {
        $options = ['method' => 'GET', 'data' => $params];
        $query = http_build_query($options['data']);

        if (!empty($query)) {
            $url .= Url::hasQuery($url) ? '&' . $query : '?' . $query;
        }

        unset($options['data']);
        return new static($url, $options);
    }

    /**
     * <p>Decode the json response content</p>
     * @param bool $array
     * @return array|stdClass|null
     */
    public function json(bool $array = true): array|stdClass|null
    {
        try {
            return json_decode($this->getContent(), $array, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
    }

    /**
     * @return Closure
     */
    public function prepareHeaders(): Closure
    {
        return function ($curl, $header): int {
            $parts = Str::split($header, ':');

            if (empty($parts[0]) === false && empty($parts[1]) === false) {
                $key = array_shift($parts);
                $this->headers[$key] = implode(':', $parts);
            }

            return strlen($header);
        };
    }

    /**
     * <p>Prepare data for post fields</p>
     * @param array|string $data
     * @return string
     */
    protected function preparePostFields(mixed $data): string
    {
        if (is_object($data) || is_array($data)) {
            return http_build_query($data);
        }

        return $data;
    }

    /**
     * <p>Return the http status code</p>
     * @return int|null
     */
    public function getStatusCode(): ?int
    {
        return $this->info['http_code'] ?? null;
    }

    /**
     * <p>Return the response content</p>
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * <p>Return the response headers</p>
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * <p>Return the response info</p>
     * @return array
     */
    public function getInfo(): array
    {
        return $this->info;
    }

    /**
     * <p>Return the error code</p>
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * <p>Return the error message</p>
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * <p>Return the request method</p>
     * @return string
     */
    public function getMethod(): string
    {
        return $this->options['method'];
    }

    /**
     * <p>Return the request URL</p>
     * @return string
     */
    public function getUrl(): string
    {
        return $this->options['url'];
    }

    /**
     * <p>Return the request options</p>
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return void
     */
    private function prepareCurlOptions(): void
    {
        switch (HttpMethodsType::fromName(Str::upper($this->options['method']))) {
            case HttpMethodsType::POST:
                $this->curlOpt[CURLOPT_POST] = true;
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = 'POST';
                $this->curlOpt[CURLOPT_POSTFIELDS] = $this->preparePostFields($this->options['data']);
                break;
            case HttpMethodsType::PUT:
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = 'PUT';
                $this->curlOpt[CURLOPT_POSTFIELDS] = $this->preparePostFields($this->options['data']);

                if ($this->options['file']) {
                    $this->curlOpt[CURLOPT_INFILE] = fopen($this->options['file'], 'rb');
                    $this->curlOpt[CURLOPT_INFILESIZE] = File::size($this->options['file']) ?? 0;
                }
                break;
            case HttpMethodsType::PATCH:
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = 'PATCH';
                $this->curlOpt[CURLOPT_POSTFIELDS] = $this->preparePostFields($this->options['data']);
                break;
            case HttpMethodsType::DELETE:
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                $this->curlOpt[CURLOPT_POSTFIELDS] = $this->preparePostFields($this->options['data']);
                break;
            case HttpMethodsType::HEAD:
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = 'HEAD';
                $this->curlOpt[CURLOPT_POSTFIELDS] = $this->preparePostFields($this->options['data']);
                $this->curlOpt[CURLOPT_NOBODY] = true;
                break;
            case HttpMethodsType::GET:
                $this->curlOpt[CURLOPT_CUSTOMREQUEST] = 'GET';
                break;
        }
    }
}