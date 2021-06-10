<?php


namespace SendThen\Authentication;


class AccessTokenAuth
{
    protected string $accessToken;

    /**
     * AccessTokenAuth constructor.
     * @param string $accessToken
     */
    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken?:getenv('SENDTHEN_ACCESS_TOKEN');
    }

    /**
     * @return array|false|string
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }
}