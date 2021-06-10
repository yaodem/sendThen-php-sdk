<?php


namespace SendThen;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;

use SendThen\Actions\Attributes\Page;
use SendThenException;


class Connection
{
    const API_END_POINT = 'https://localhost:3000/api';
    const INVALID_ACCESS_TOKEN_CODE = '001';
    const INVALID_ACCESS_TOKEN_DESCRIPTION = 'Invalid access Token';
    const TIME_OUT = 10;

    protected string $accessToken;

    protected string $clientId;

    private Client $client;

    /**
     * Connection constructor.
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->client();
        $this->accessToken = $accessToken?:getenv('SENDTHEN_ACCESS_TOKEN');
    }

    /**
     * @return array|false|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }


    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }


    /**
     * @param string $method
     * @param string $endPoint
     * @param null $body
     * @param array $params
     * @param array $headers
     * @return Request
     * @throws SendThenException
     */
    private function sendRequest(string $method = 'GET', string $endPoint, $body = null, array $params = [], array $headers = []): Request
    {
        $_headers = array_merge($headers,
            [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer '{$this->getAccessToken()}'",
                'User-Agent' => "SendThen/PHP-SDK " . Version::DEFAULT_API_VERSION
            ]
        );

        if(empty($this->getAccessToken()))
        {
            throw new SendThenException(self::INVALID_ACCESS_TOKEN_DESCRIPTION, 12);
        }

        if(empty($params))
        {
            $endPoint .= '?' . http_build_query($params);
        }

        return new Request($method, $endPoint, $_headers, $body);
    }

    /**
     * @param string $uri
     * @param array $params
     * @param bool $fetchAll
     * @return mixed
     * @throws SendThenException
     */
    public function get(string $uri, array $params = [], bool $fetchAll = false )
    {
        try {
            if($fetchAll && !array_keys($params, 'page'))
            {
                $params['page'] = new Page(100);
            }

            $request = $this->sendRequest('GET', $this->formatUrl($uri, 'GET'), json_encode($params));

            $response = $this->client()->send($request);

            $json = $this->parseResponseData($response);

            if(!$fetchAll) return $json;

            if($this->hasMoreData($json, $params['page']))
            {
                do {
                    $params['page']->next();
                    $nextPage = $this->get($uri, $params);
                    $json = array_merge_recursive($json, $nextPage);

                }while ($this->hasMoreData($nextPage, $params['page']));
            }

            return $json;
        }catch (SendThenException | GuzzleException $e)
        {
            throw new SendThenException($e->getMessage());
        }
    }

    /**
     * @param string $uri
     * @param array $body
     * @return mixed
     * @throws SendThenException
     */
    public function post(string $uri, array $body = [])
    {
        try {
                $request = $this->sendRequest('POST', $this->formatUrl($uri, 'post'), $body);
                $response = $this->getClient()->send($request);
                return $this->parseResponseData($response);
        }catch (SendThenException | GuzzleException $e)
        {
            throw new SendThenException($e->getMessage());
        }
    }

    /**
     * @param string $uri
     * @param array $body
     * @return mixed
     * @throws SendThenException
     */
    public function patch(string $uri, array $body = [])
    {
        try {
                $request = $this->sendRequest('PUT', $this->formatUrl($uri, 'put'), $body);
                $response = $this->getClient()->send($request);
                return $this->parseResponseData($response);
        }catch (SendThenException | GuzzleException $exception)
        {
            throw new SendThenException($exception->getMessage());
        }
    }


    /**
     * @param string $uri
     * @param null $method
     * @return string
     */
    private function formatUrl (string $uri, $method = null): string
    {
        return self::API_END_POINT . $uri;
    }

    /**
     * @param Response $response
     * @return mixed
     */
    private function parseResponseData(Response $response)
    {
        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * @return Client
     */
    private function client():Client
    {
        if($this->client)
        {
            return $this->client;
        }

        $this->client = new Client(
            [
                'http_errors' => true,
                'expect' => false
            ]
        );

        return $this->client;
    }

    /**
     * @param array $json
     * @param Page $page
     * @return bool
     */
    private function hasMoreData(array $json, Page $page): bool
    {
        return count($json['data'] ?? []) === $page->getSize();
    }
}