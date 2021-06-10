<?php


namespace SendThen\Exceptions;


class ValidationException extends \SendThenException
{
    public array $errors;

    /**
     * ValidationException constructor.
     * @param string $message
     * @param int $code
     * @param array $errors
     */
    public function __construct($message = "", $code = 0, array $errors = [])
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }
}