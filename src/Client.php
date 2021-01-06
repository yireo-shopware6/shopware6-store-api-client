<?php declare(strict_types=1);

namespace Yireo\StoreApiClient;

use Exception;
use GuzzleHttp\Client as HttpClient;

/**
 * Main client class for the Shopware 6 Store API
 */
class Client
{
    const VERSION = 4;

    /** @var string */
    private $baseUri = '';

    /** @var string */
    private $accessKey = '';

    /** @var string */
    private $token = '';

    /** @var HttpClient */
    private $httpClient;

    /**
     * ApiClient constructor.
     * @param string $baseUri
     * @param string $accessKey
     */
    public function __construct(string $baseUri, string $accessKey)
    {
        $this->baseUri = $baseUri;
        $this->accessKey = $accessKey;
    }

    /**
     * @return bool
     * @throws Exception
     * @todo Create a custom exception for this
     */
    public function validate(): bool
    {
        if (empty($this->accessKey)) {
            throw new Exception('Access key is not set');
        }

        return true;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        $data = $this->get('context');
        if (isset($data['token'])) {
            $this->token = $data['token'];
        }

        return $data;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        $options = ['json' => ['includes' => ['sales_channel_context' => ['token']]]];
        $data = $this->get('context', $options);
        if (isset($data['token'])) {
            $this->token = $data['token'];
            return $data['token'];
        }

        return '';
    }


    /**
     * @return array
     */
    public function getProducts(): array
    {
        return $this->get('product');
    }

    /**
     * @return array
     */
    public function getCategories(): array
    {
        return $this->get('category');
    }


    /**
     * @param string $path
     * @param array $options
     * @return array
     */
    public function get(string $path, array $options = []): array
    {
        $path = 'v' . self::VERSION . '/' . $path;
        return $this->request('get', $path, $options);
    }

    /**
     * @param string $path
     * @param array $postData
     * @param array $options
     * @return array
     */
    public function post(string $path, array $postData, array $options = []): array
    {
        $options['json'] = $postData;
        return $this->request('post', $path, $options);
    }

    /**
     * @param string $method
     * @param string $path
     * @param array $options
     * @return string
     */
    private function request(string $method, string $path, array $options = []): array
    {
        if (empty($options['headers'])) {
            $options['headers'] = $this->getHeaders();
        }

        $response = $this->getHttpClient()->request($method, 'store-api/' . $path, $options);
        //$response = $this->getHttpClient()->request($method, 'sales-channel-api/' . $path, $options);
        $json = (string)$response->getBody();
        return json_decode($json, true);
    }

    /**
     * @return HttpClient
     */
    private function getHttpClient(): HttpClient
    {
        if ($this->httpClient) {
            return $this->httpClient;
        }

        $this->httpClient = new HttpClient(['base_uri' => $this->baseUri]);
        return $this->httpClient;
    }

    /**
     * @return string[]
     */
    private function getHeaders(): array
    {
        $headers = [
            //'SW-Access-Key' => $this->accessKey,
            'sw-access-key' => $this->accessKey,
            'Content-Type' => 'application/json'
        ];

        if (!empty($this->token)) {
            $headers['sw-context-token'] = $this->token;
        }

        return $headers;
    }
}
