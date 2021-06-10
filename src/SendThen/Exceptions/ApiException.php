<?php


namespace SendThen\Exceptions;


class ApiException extends \SendThenException
{
    private $request;
    private $response;

    /**
     * ApiException constructor.
     * @param string $message
     * @param int $code
     * @param $request
     * @param $response
     */
    public function __construct($message = "", $code = 0, $request, $response  )
    {
        parent::__construct($message, $code);
        $this->request = $request;
        $this->response = $response;
    }

    /**
     * @return mixed
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}